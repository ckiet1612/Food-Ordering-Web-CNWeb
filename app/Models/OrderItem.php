<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class OrderItem extends Model
{
    protected static string $table = 'order_items';

    public static function byOrder(int $orderId): array
    {
        $stmt = static::db()->prepare(
            'SELECT oi.*, f.name, f.slug, f.image, f.bg_color FROM order_items oi
             JOIN foods f ON f.id = oi.food_id
             WHERE oi.order_id = ?'
        );
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }
}
