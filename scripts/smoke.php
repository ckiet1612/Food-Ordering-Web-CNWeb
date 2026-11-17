<?php
declare(strict_types=1);

// Kiểm thử tích hợp có ghi: chỉ tạo tài khoản/đơn có nhãn riêng, không xoá dữ liệu.
if (PHP_SAPI !== 'cli' || !in_array('--write', $argv, true)) {
    exit("Chay: php scripts/smoke.php --write\nSe tao tai khoan va don kiem thu, khong xoa sau khi chay.\n");
}
define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/app/bootstrap.php';
use App\Core\Database;
use App\Models\Order;

$base = rtrim(getenv('TEST_URL') ?: config('app')['url'], '/');
$run = 'YUMIO-E2E-' . date('Ymd-His') . '-' . bin2hex(random_bytes(2));
$dir = BASE_PATH . '/.runtime/' . $run;
if (!is_dir($dir)) { mkdir($dir, 0777, true); }
$checks = 0;
$created = [];
function check(bool $value, string $message): void {
    global $checks;
    if (!$value) { throw new RuntimeException('FAIL: ' . $message); }
    ++$checks;
    echo "PASS {$checks}: {$message}\n";
}
function request(string $client, string $path, ?array $data = null, bool $json = false): array {
    global $base, $dir;
    $ch = curl_init($base . $path);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_COOKIEJAR => "$dir/$client.cookies", CURLOPT_COOKIEFILE => "$dir/$client.cookies", CURLOPT_TIMEOUT => 15, CURLOPT_FOLLOWLOCATION => false]);
    if ($data !== null) {
        $multipart = count(array_filter($data, fn ($value) => $value instanceof CURLFile)) > 0;
        curl_setopt($ch, CURLOPT_POSTFIELDS, $multipart ? $data : http_build_query($data));
    }
    if ($json) { curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']); }
    curl_setopt($ch, CURLOPT_HEADER, true);
    $response = curl_exec($ch);
    if ($response === false) { throw new RuntimeException(curl_error($ch)); }
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headers = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);
    curl_close($ch);
    if (preg_match('/Fatal error|Warning:|Uncaught|Deprecated:/i', $body)) {
        file_put_contents("$dir/error.html", $body);
        throw new RuntimeException("PHP error at {$path}; see {$dir}/error.html");
    }
    preg_match('/^Location:\s*(.+)$/mi', $headers, $match);
    $decoded = null;
    if ($json) {
        try { $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR); }
        catch (JsonException) { throw new RuntimeException("Invalid JSON response: HTTP {$code} at {$path}"); }
    }
    return ['code' => $code, 'body' => $body, 'location' => trim($match[1] ?? ''), 'json' => $decoded];
}
function field(array $response, string $name): string {
    if (!preg_match('/name="' . preg_quote($name, '/') . '"[^>]*value="([^"]*)"/', $response['body'], $m)) {
        throw new RuntimeException('Missing form field: ' . $name);
    }
    return html_entity_decode($m[1], ENT_QUOTES, 'UTF-8');
}
function csrf(string $client, string $path = '/'): string { return field(request($client, $path), 'csrf_token'); }
function orderRow(int $id): array {
    $s = Database::connection()->prepare('SELECT * FROM orders WHERE id = ?'); $s->execute([$id]); return $s->fetch();
}
try {
    $db = Database::connection();
    $baseline = $db->query('SELECT COUNT(*) FROM orders')->fetchColumn();
    $home = request('buyer', '/');
    check($home['code'] === 200 && str_contains($home['body'], 'YUMIO'), 'Home renders without PHP errors');
    foreach (['/menu', '/foods/ga-ran-truyen-thong', '/search?q=burger', '/cart'] as $path) {
        check(request('buyer', $path)['code'] === 200, 'GET ' . $path);
    }
    check(request('buyer', '/menu/ga-ran')['location'] === $base . '/menu#menu-category-1', 'Legacy category route redirects to continuous menu section');
    check(request('guest', '/checkout')['location'] === $base . '/login', 'Checkout requires login');
    check(request('buyer', '/cart/add', ['food_id' => 1])['code'] === 419, 'CSRF required on cart');
    $token = csrf('buyer', '/login');
    $r = request('buyer', '/cart/add', ['csrf_token' => $token, 'food_id' => 1, 'quantity' => 2], true);
    check($r['code'] === 200 && $r['json']['count'] === 2 && $r['json']['total'] === format_price(159000), 'Cart add: 2 x 72000 + 15000 shipping');
    $r = request('buyer', '/cart/update', ['csrf_token' => $token, 'food_id' => 1, 'quantity' => 3], true);
    check($r['json']['count'] === 3 && $r['json']['shipping_fee'] === 'Miễn phí' && $r['json']['total'] === format_price(216000), 'Cart increase and free shipping threshold');
    $r = request('buyer', '/cart/update', ['csrf_token' => $token, 'food_id' => 1, 'quantity' => 1], true);
    check($r['json']['count'] === 1 && $r['json']['total'] === format_price(87000), 'Cart decrease recalculates total');
    request('buyer', '/cart/add', ['csrf_token' => $token, 'food_id' => 2], true);
    $r = request('buyer', '/cart/remove', ['csrf_token' => $token, 'food_id' => 2], true);
    check($r['json']['count'] === 1, 'Cart remove');
    check(request('buyer', '/cart/add', ['csrf_token' => $token, 'food_id' => 99999999], true)['code'] === 422, 'Unavailable food rejected');
    foreach ([0, -1, 51, '1.5', ['2']] as $quantity) {
        check(request('buyer', '/cart/update', ['csrf_token' => $token, 'food_id' => 1, 'quantity' => $quantity], true)['code'] === 422, 'Reject invalid cart quantity ' . json_encode($quantity));
    }

    $email = strtolower($run) . '@example.test';
    $password = bin2hex(random_bytes(10));
    $r = request('buyer', '/register', ['csrf_token' => $token, 'name' => $run, 'email' => $email, 'phone' => '0900000000', 'password' => $password, 'password_confirmation' => $password, 'role' => 'admin']);
    check($r['code'] === 302, 'Register test customer over HTTP');
    $s = $db->prepare('SELECT * FROM users WHERE email = ?'); $s->execute([$email]); $buyer = $s->fetch();
    check($buyer && $buyer['role'] === 'customer' && password_verify($password, $buyer['password']), 'MySQL stores hashed password and customer role');
    $malformed = ['csrf_token' => $token, 'receiver_name' => ['bad'], 'phone' => ['bad'], 'address' => ['bad'], 'payment_method' => ['cod']];
    $page = request('buyer', '/checkout');
    check($page['code'] === 200 && str_contains($page['body'], 'Xem lại đơn hàng'), 'Checkout renders review action');
    $payload = ['csrf_token' => field($page, 'csrf_token'), 'checkout_token' => field($page, 'checkout_token'), 'receiver_name' => $run, 'phone' => '0900000000', 'address' => 'Địa chỉ kiểm thử · 1 Đại Cồ Việt, Hà Nội', 'note' => $run, 'payment_method' => 'cod', 'subtotal' => 1, 'shipping_fee' => 0, 'total_amount' => 1, 'checkout_action' => 'confirm'];
    $bad = $payload; $bad['phone'] = '123';
    check(request('buyer', '/checkout', $bad)['location'] === $base . '/checkout', 'Invalid checkout rejected');
    check((int) $db->query('SELECT COUNT(*) FROM orders')->fetchColumn() === (int) $baseline, 'Invalid checkout creates no order');
    $malformed['checkout_token'] = $payload['checkout_token'];
    check(request('buyer', '/checkout', $malformed)['location'] === $base . '/checkout', 'Malformed checkout arrays rejected');
    $page = request('buyer', '/checkout');
    check($page['code'] === 200, 'Validation error renders without array conversion warnings');
    $oldToken = $payload['checkout_token'];
    $payload['checkout_token'] = field($page, 'checkout_token');
    check(request('buyer', '/checkout', array_replace($payload, ['checkout_token' => $oldToken]))['location'] === $base . '/checkout', 'Old checkout tab cannot submit after a new checkout opens');
    check(request('buyer', '/checkout/review', $payload)['code'] === 200, 'Review renders before saving order');
    check((int) $db->query('SELECT COUNT(*) FROM orders')->fetchColumn() === (int) $baseline, 'Review creates no order');
    $r = request('buyer', '/checkout', $payload);
    check((bool) preg_match('#/orders/(\d+)$#', $r['location'], $m), 'COD redirects to saved order');
    $id = (int) $m[1]; $created[] = $id; $order = orderRow($id);
    check((int) $order['total_amount'] === 87000 && (int) $order['shipping_fee'] === 15000 && $order['payment_method'] === 'cod' && $order['status'] === 'pending', 'MySQL saves authoritative total, fee and COD; ignores forged totals');
    check(count(App\Models\OrderItem::byOrder($id)) === 1 && count(Order::events($id)) === 1, 'Order items and first event persisted');
    check(request('buyer', '/checkout', $payload)['location'] === $r['location'] && (int) $db->query('SELECT COUNT(*) FROM orders')->fetchColumn() === (int) $baseline + 1, 'Repeated checkout creates no duplicate order');
    check(str_contains(request('buyer', '/cart')['body'], 'Giỏ hàng đang trống'), 'Cart clears only after successful order');
    check(str_contains(request('buyer', '/orders')['body'], '#' . $id) && request('buyer', '/orders/' . $id)['code'] === 200, 'Customer sees history and order detail');
    check(request('buyer', '/admin')['code'] === 403 && request('buyer', '/shipper')['code'] === 403, 'Customer cannot access admin or delivery routes');
    $before = (int) $db->query('SELECT COUNT(*) FROM orders')->fetchColumn();
    $food = App\Models\Food::find(1);
    try {
        Order::place((int) $buyer['id'], ['receiver_name' => $run, 'phone' => '0900000000', 'address' => 'Test', 'checkout_token' => bin2hex(random_bytes(32))], ['items' => [array_replace($food, ['quantity' => 1, 'price' => (int) $food['price'] + 1])]]);
        check(false, 'Stale price must fail');
    } catch (DomainException) {
        check(!$db->inTransaction() && (int) $db->query('SELECT COUNT(*) FROM orders')->fetchColumn() === $before, 'Stale price rolls back transaction without saving order');
    }

    $adminToken = csrf('admin', '/login');
    request('admin', '/login', ['csrf_token' => $adminToken, 'email' => getenv('TEST_ADMIN_EMAIL') ?: 'admin@yumio.vn', 'password' => getenv('TEST_ADMIN_PASSWORD') ?: 'admin123']);
    check(request('admin', '/admin')['code'] === 200, 'Demo admin login and dashboard');
    $adminToken = csrf('admin', '/admin/orders/' . $id);
    request('admin', "/admin/orders/$id/status", ['csrf_token' => $adminToken, 'status' => 'completed']);
    check(orderRow($id)['status'] === 'pending', 'Reject skipping pending to completed');
    request('admin', "/admin/orders/$id/status", ['csrf_token' => $adminToken, 'status' => 'confirmed']);
    check(orderRow($id)['status'] === 'confirmed', 'Admin confirms order');
    request('buyer', "/orders/$id/cancel", ['csrf_token' => $payload['csrf_token']]);
    check(orderRow($id)['status'] === 'confirmed', 'Customer cannot cancel confirmed order');
    request('admin', "/admin/orders/$id/status", ['csrf_token' => $adminToken, 'status' => 'preparing']);
    request('admin', "/admin/orders/$id/status", ['csrf_token' => $adminToken, 'status' => 'delivering']);
    check(orderRow($id)['status'] === 'preparing', 'Delivery requires assigned shipper');
    $shipperId = (int) $db->query("SELECT id FROM users WHERE email = 'shipper@yumio.vn' AND role = 'shipper'")->fetchColumn();
    request('admin', "/admin/orders/$id/assign", ['csrf_token' => $adminToken, 'shipper_id' => $shipperId]);
    check((int) orderRow($id)['shipper_id'] === $shipperId, 'Admin assigns demo shipper');
    $shipperToken = csrf('shipper', '/login');
    request('shipper', '/login', ['csrf_token' => $shipperToken, 'email' => 'shipper@yumio.vn', 'password' => '123456']);
    check(request('shipper', '/shipper')['code'] === 200 && request('shipper', '/admin')['code'] === 403, 'Demo shipper login with restricted role');
    $shipperToken = csrf('shipper', '/shipper/orders/' . $id);
    request('shipper', "/shipper/orders/$id/status", ['csrf_token' => $shipperToken, 'status' => 'delivering']);
    check(orderRow($id)['status'] === 'delivering', 'Assigned shipper starts delivery');
    request('shipper', "/shipper/orders/$id/status", ['csrf_token' => $shipperToken, 'status' => 'completed']);
    check(orderRow($id)['status'] === 'completed', 'Assigned shipper completes delivery');
    check(count(Order::events($id)) === 6, 'Six order events persisted');
    request('admin', "/admin/orders/$id/status", ['csrf_token' => $adminToken, 'status' => 'pending']);
    check(orderRow($id)['status'] === 'completed', 'Completed order cannot be reopened');

    $token = csrf('buyer', '/cart');
    request('buyer', '/cart/add', ['csrf_token' => $token, 'food_id' => 1, 'quantity' => 3], true);
    $page = request('buyer', '/checkout');
    $payload['csrf_token'] = field($page, 'csrf_token'); $payload['checkout_token'] = field($page, 'checkout_token');
    $payload['delivery_lat'] = '21.012'; $payload['delivery_lng'] = '105.85';
    $bad = $payload; $bad['delivery_lat'] = '999';
    check(request('buyer', '/checkout', $bad)['location'] === $base . '/checkout', 'Invalid coordinates rejected without losing cart');
    check(request('buyer', '/checkout/review', $payload)['code'] === 200, 'Review accepts valid map coordinates');
    $r = request('buyer', '/checkout', $payload); preg_match('#/orders/(\d+)$#', $r['location'], $m);
    $id2 = (int) ($m[1] ?? 0); $created[] = $id2; $row2 = orderRow($id2);
    check((int) $row2['total_amount'] === 216000 && (int) $row2['shipping_fee'] === 0 && (float) $row2['distance_km'] > 0, 'Map coordinates and distance persisted; free shipping preserved');
    check(request('buyer', '/orders/' . $id2)['code'] === 200, 'Order with map renders');
    request('buyer', "/orders/$id2/cancel", ['csrf_token' => $token]);
    check(orderRow($id2)['status'] === 'cancelled', 'Pending order can be cancelled');
    check(request('shipper', '/shipper/orders/' . $id2)['code'] === 404, 'Shipper cannot view unassigned order');
    $token = csrf('other', '/login');
    request('other', '/login', ['csrf_token' => $token, 'email' => 'khach@yumio.vn', 'password' => '123456']);
    check(request('other', '/orders/' . $id)['code'] === 404, 'Other customer cannot view test order');
    request('other', "/orders/$id2/cancel", ['csrf_token' => $token]);
    check(orderRow($id2)['status'] === 'cancelled', 'Other customer cannot change order');

    // CRUD chỉ trên bản ghi vừa tạo của ca test; không sửa món/danh mục dữ liệu gốc.
    foreach (['/admin/foods', '/admin/categories', '/admin/users'] as $path) {
        check(request('admin', $path)['code'] === 200, 'Admin list ' . $path);
    }
    $categoryName = $run . '-CATEGORY';
    request('admin', '/admin/categories', ['csrf_token' => $adminToken, 'name' => $categoryName, 'description' => 'Danh mục kiểm thử', 'color' => '#f9c94d', 'sort_order' => 999]);
    $s = $db->prepare('SELECT * FROM categories WHERE name = ?'); $s->execute([$categoryName]); $category = $s->fetch();
    check((bool) $category, 'Admin creates test category in MySQL');
    $foodName = $run . '-FOOD';
    $fixture = $dir . '/fixture.png';
    file_put_contents($fixture, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Wl6IZAAAAAASUVORK5CYII='));
    request('admin', '/admin/foods', ['csrf_token' => $adminToken, 'name' => $foodName, 'category_id' => $category['id'], 'price' => 41000, 'description' => 'Món kiểm thử', 'bg_color' => '#f9c94d', 'is_available' => 1, 'image' => new CURLFile($fixture, 'image/png', 'fixture.png')]);
    $s = $db->prepare('SELECT * FROM foods WHERE name = ?'); $s->execute([$foodName]); $testFood = $s->fetch();
    check((bool) $testFood && (int) $testFood['price'] === 41000, 'Admin creates test food in MySQL');
    check(str_ends_with($testFood['image'] ?? '', '.png') && is_file(BASE_PATH . '/public/' . $testFood['image']), 'Admin upload validates MIME and saves local PNG');
    request('admin', '/admin/foods/' . $testFood['id'], ['csrf_token' => $adminToken, 'name' => $foodName, 'category_id' => $category['id'], 'price' => 42000, 'description' => 'Món kiểm thử đã sửa, ngừng bán sau test', 'bg_color' => '#9cc94a']);
    $testFood = App\Models\Food::find((int) $testFood['id']);
    check((int) $testFood['price'] === 42000 && !(int) $testFood['is_available'], 'Admin edits price and marks only test food unavailable');
    request('admin', '/admin/categories/' . $category['id'], ['csrf_token' => $adminToken, 'name' => $categoryName, 'description' => 'Danh mục kiểm thử được giữ lại, không xoá', 'color' => '#9cc94a', 'sort_order' => 999]);
    check(App\Models\Category::find((int) $category['id'])['color'] === '#9cc94a', 'Admin edits test category');
    check(request('buyer', '/cart/add', ['csrf_token' => csrf('buyer', '/cart'), 'food_id' => $testFood['id']], true)['code'] === 422, 'Unavailable test food cannot be ordered');

    $result = ['run' => $run, 'checks' => $checks, 'buyer_email' => $email, 'order_ids' => $created, 'test_category_id' => $category['id'], 'test_food_id' => $testFood['id'], 'baseline_orders' => (int) $baseline];
    file_put_contents($dir . '/result.json', json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage() . "\nRun: $run\nCreated orders: " . implode(',', $created) . "\n");
    exit(1);
}
