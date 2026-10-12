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
        'delivering' => 'Đang giao',
        'completed'  => 'Hoàn thành',
        'cancelled'  => 'Đã huỷ',
    ];

    public const PAYMENT_METHODS = [
        'cod'  => 'Thanh toán khi nhận hàng',
        'bank' => 'Chuyển khoản ngân hàng',
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
            $orderId = static::create($info + [
                'user_id'      => $userId,
                'subtotal'     => $cart['subtotal'],
                'shipping_fee' => $cart['shipping_fee'],
                'total_amount' => $cart['total'],
            ]);

            foreach ($cart['items'] as $item) {
                OrderItem::create([
                    'order_id' => $orderId,
                    'food_id'  => $item['id'],
                    'quantity' => $item['quantity'],
                    'price'    => $item['price'],
                ]);
            }

            $db->commit();
            return $orderId;
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
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
