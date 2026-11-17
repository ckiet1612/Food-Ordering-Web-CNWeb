<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/app/bootstrap.php';

// DDL MySQL tự commit. Kiểm tra từng cột để có thể chạy tiếp nếu lần trước bị ngắt.
$db = App\Core\Database::connection();
$db->exec("ALTER TABLE users MODIFY role ENUM('customer','admin','shipper') NOT NULL DEFAULT 'customer'");
$db->exec("ALTER TABLE orders MODIFY status ENUM('pending','confirmed','preparing','delivering','completed','cancelled') NOT NULL DEFAULT 'pending'");
$columns = [
    'orders' => [
        'shipper_id' => 'INT UNSIGNED NULL',
        'checkout_token' => 'CHAR(64) NULL UNIQUE',
        'delivery_lat' => 'DECIMAL(10,7) NULL',
        'delivery_lng' => 'DECIMAL(10,7) NULL',
        'distance_km' => 'DECIMAL(10,2) NULL',
    ],
    'order_items' => ['food_name' => 'VARCHAR(150) NULL'],
];
foreach ($columns as $table => $fields) {
    foreach ($fields as $column => $definition) {
        $stmt = $db->prepare('SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?');
        $stmt->execute([$table, $column]);
        if (!(int) $stmt->fetchColumn()) {
            $db->exec("ALTER TABLE {$table} ADD COLUMN {$column} {$definition}");
        }
    }
}
$constraint = $db->query("SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'shipper_id' AND REFERENCED_TABLE_NAME = 'users'");
if (!(int) $constraint->fetchColumn()) {
    $db->exec('ALTER TABLE orders ADD CONSTRAINT fk_orders_shipper FOREIGN KEY (shipper_id) REFERENCES users(id) ON DELETE RESTRICT');
}
$sql = file_get_contents(BASE_PATH . '/database/migrations/001_delivery.sql');
$db->exec(substr($sql, strpos($sql, 'CREATE TABLE')));
echo "Migration giao hang hoan tat. Du lieu cu duoc giu nguyen.\n";
