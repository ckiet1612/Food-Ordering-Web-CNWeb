<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Order extends Model
{
    protected static string $table = 'orders';

    public const STATUSES = [
        'pending'    => 'Chờ xác nhận',
        'confirmed'  => 'Đã xác nhận',
        'preparing'  => 'Đang chuẩn bị',
        'delivering' => 'Đang giao',
        'completed'  => 'Hoàn thành',
        'cancelled'  => 'Đã huỷ',
    ];

    public const PAYMENT_METHODS = [
        'cod'  => 'Thanh toán khi nhận hàng',
        'bank' => 'Chuyển khoản ngân hàng',
    ];

    public const TRANSITIONS = [
        'pending' => ['confirmed', 'cancelled'],
        'confirmed' => ['preparing', 'cancelled'],
        'preparing' => ['delivering', 'cancelled'],
        'delivering' => ['completed'],
        'completed' => [],
        'cancelled' => [],
    ];

    public const SHIPPING_FEE = 15000;
    public const FREE_SHIPPING_FROM = 200000;

    public static function shippingFee(int|float $subtotal): int
    {
        return $subtotal >= self::FREE_SHIPPING_FROM ? 0 : self::SHIPPING_FEE;
    }

    // Tạo đơn hàng + chi tiết đơn trong 1 transaction
    public static function place(int $userId, array $info, array $cart): int
    {
        $db = static::db();
        $db->beginTransaction();

        try {
            // Khoá các món theo ID cố định; giá và tổng tiền luôn lấy từ MySQL.
            $items = $cart['items'];
            usort($items, fn ($a, $b) => $a['id'] <=> $b['id']);
            if ($items === []) {
                throw new \DomainException('Giỏ hàng đang trống.');
            }
            $subtotal = 0;
            $stmt = $db->prepare('SELECT name, price, is_available FROM foods WHERE id = ? FOR UPDATE');
            foreach ($items as &$item) {
                $stmt->execute([$item['id']]);
                $food = $stmt->fetch();
                if (!$food || !$food['is_available'] || (int) $food['price'] !== (int) $item['price']) {
                    throw new \DomainException('Món hoặc giá đã thay đổi. Vui lòng kiểm tra lại giỏ hàng.');
                }
                if (!is_int($item['quantity']) || $item['quantity'] < 1 || $item['quantity'] > Cart::MAX_QUANTITY) {
                    throw new \DomainException('Số lượng món không hợp lệ.');
                }
                $subtotal += (int) $food['price'] * $item['quantity'];
                $item['name'] = $food['name'];
            }
            unset($item);
            $shipping = self::shippingFee($subtotal);
            $orderId = static::create([
                'user_id'      => $userId,
                'receiver_name' => $info['receiver_name'],
                'phone' => $info['phone'],
                'address' => $info['address'],
                'note' => $info['note'] ?? null,
                'payment_method' => 'cod',
                'checkout_token' => $info['checkout_token'],
                'delivery_lat' => $info['delivery_lat'] ?? null,
                'delivery_lng' => $info['delivery_lng'] ?? null,
                'distance_km' => $info['distance_km'] ?? null,
                'subtotal'     => $subtotal,
                'shipping_fee' => $shipping,
                'total_amount' => $subtotal + $shipping,
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $orderId,
                    'food_id'  => $item['id'],
                    'quantity' => $item['quantity'],
                    'price'    => $item['price'],
                    'food_name' => $item['name'],
                ]);
            }

            self::recordEvent($orderId, 'pending', $userId, 'Khách hàng đặt đơn');

            $db->commit();
            return $orderId;
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function byCheckoutToken(int $userId, string $token): ?array
    {
        $stmt = static::db()->prepare('SELECT * FROM orders WHERE user_id = ? AND checkout_token = ?');
        $stmt->execute([$userId, $token]);
        return $stmt->fetch() ?: null;
    }

    // Cùng một khoá dòng cho cập nhật trạng thái, huỷ và phân công: tránh ghi đè đồng thời.
    public static function transition(int $id, string $next, int $actorId): void
    {
        $db = static::db();
        $db->beginTransaction();
        try {
            $stmt = $db->prepare('SELECT * FROM orders WHERE id = ? FOR UPDATE');
            $stmt->execute([$id]);
            $order = $stmt->fetch();
            $actor = User::find($actorId);
            if (!$order || !$actor) {
                throw new \DomainException('Đơn hàng hoặc người dùng không tồn tại.');
            }
            $allowed = $actor['role'] === 'admin'
                || ($actor['role'] === 'shipper' && (int) $order['shipper_id'] === $actorId && in_array($next, ['delivering', 'completed'], true))
                || ((int) $order['user_id'] === $actorId && $next === 'cancelled' && $order['status'] === 'pending');
            if (!$allowed || !in_array($next, self::TRANSITIONS[$order['status']] ?? [], true)) {
                throw new \DomainException('Không thể chuyển trạng thái này. Vui lòng tải lại đơn hàng.');
            }
            if ($next === 'delivering' && !$order['shipper_id']) {
                throw new \DomainException('Hãy phân công nhân viên giao hàng trước khi giao.');
            }
            static::update($id, ['status' => $next]);
            self::recordEvent($id, $next, $actorId, self::STATUSES[$next]);
            $db->commit();
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public static function assign(int $id, int $shipperId, int $actorId): void
    {
        $db = static::db();
        $db->beginTransaction();
        try {
            $stmt = $db->prepare('SELECT * FROM orders WHERE id = ? FOR UPDATE');
            $stmt->execute([$id]);
            $order = $stmt->fetch();
            $actor = User::find($actorId);
            $shipper = User::find($shipperId);
            if (!$order || ($actor['role'] ?? '') !== 'admin' || ($shipper['role'] ?? '') !== 'shipper'
                || !in_array($order['status'], ['confirmed', 'preparing'], true)) {
                throw new \DomainException('Chỉ phân công tài xế hợp lệ cho đơn đã xác nhận hoặc đang chuẩn bị.');
            }
            static::update($id, ['shipper_id' => $shipperId]);
            self::recordEvent($id, $order['status'], $actorId, 'Phân công giao hàng: ' . $shipper['name']);
            $db->commit();
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    private static function recordEvent(int $id, string $status, int $actorId, string $note): void
    {
        $stmt = static::db()->prepare('INSERT INTO order_events (order_id, status, actor_id, note) VALUES (?, ?, ?, ?)');
        $stmt->execute([$id, $status, $actorId, $note]);
    }

    public static function events(int $id): array
    {
        $stmt = static::db()->prepare('SELECT * FROM order_events WHERE order_id = ? ORDER BY id');
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }

    public static function forShipper(int $shipperId): array
    {
        $stmt = static::db()->prepare("SELECT * FROM orders WHERE shipper_id = ? ORDER BY (status IN ('confirmed','preparing','delivering')) DESC, id DESC");
        $stmt->execute([$shipperId]);
        return $stmt->fetchAll();
    }

    public static function byUser(int $userId): array
    {
        $stmt = static::db()->prepare(
            'SELECT o.*, (SELECT SUM(quantity) FROM order_items WHERE order_id = o.id) AS item_count
             FROM orders o WHERE o.user_id = ? ORDER BY o.created_at DESC, o.id DESC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function filter(?string $status = null): array
    {
        $sql = 'SELECT o.*, u.email FROM orders o JOIN users u ON u.id = o.user_id';
        $params = [];

        if ($status !== null && isset(self::STATUSES[$status])) {
            $sql .= ' WHERE o.status = ?';
            $params[] = $status;
        }

        $stmt = static::db()->prepare($sql . ' ORDER BY o.created_at DESC, o.id DESC');
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function stats(): array
    {
        return static::db()->query(
            "SELECT
                COALESCE(SUM(CASE WHEN status = 'completed' THEN total_amount END), 0) AS revenue,
                COUNT(*) AS total_orders,
                SUM(status = 'pending') AS pending_orders,
                SUM(DATE(created_at) = CURDATE()) AS today_orders
             FROM orders"
        )->fetch();
    }

    public static function recent(int $limit = 6): array
    {
        $stmt = static::db()->prepare('SELECT * FROM orders ORDER BY created_at DESC, id DESC LIMIT ' . $limit);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
