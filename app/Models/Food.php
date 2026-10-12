<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Food extends Model
{
    protected static string $table = 'foods';

    public static function findBySlug(string $slug): ?array
    {
        $stmt = static::db()->prepare(
            'SELECT f.*, c.name AS category_name, c.slug AS category_slug FROM foods f
             JOIN categories c ON c.id = f.category_id
             WHERE f.slug = ?'
        );
        $stmt->execute([$slug]);
        return $stmt->fetch() ?: null;
    }

    public static function byCategory(int $categoryId): array
    {
        $stmt = static::db()->prepare('SELECT * FROM foods WHERE is_available = 1 AND category_id = ? ORDER BY id ASC');
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll();
    }

    public static function featured(int $limit = 10): array
    {
        $stmt = static::db()->prepare('SELECT * FROM foods WHERE is_available = 1 AND is_featured = 1 ORDER BY id ASC LIMIT ' . $limit);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function related(array $food, int $limit = 8): array
    {
        $stmt = static::db()->prepare(
            'SELECT * FROM foods WHERE is_available = 1 AND id <> ?
             ORDER BY (category_id = ?) DESC, RAND() LIMIT ' . $limit
        );
        $stmt->execute([$food['id'], $food['category_id']]);
        return $stmt->fetchAll();
    }

    public static function search(string $keyword): array
    {
        $stmt = static::db()->prepare('SELECT * FROM foods WHERE is_available = 1 AND name LIKE ? ORDER BY name ASC');
        $stmt->execute(['%' . $keyword . '%']);
        return $stmt->fetchAll();
    }

    public static function allWithCategory(): array
    {
        return static::db()->query(
            'SELECT f.*, c.name AS category_name FROM foods f
             JOIN categories c ON c.id = f.category_id
             ORDER BY f.id DESC'
        )->fetchAll();
    }

    public static function slugExists(string $slug, ?int $exceptId = null): bool
    {
        $stmt = static::db()->prepare('SELECT COUNT(*) FROM foods WHERE slug = ? AND id <> ?');
        $stmt->execute([$slug, $exceptId ?? 0]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public static function bestSellers(int $limit = 5): array
    {
        $stmt = static::db()->prepare(
            "SELECT f.id, f.name, f.image, f.bg_color, SUM(oi.quantity) AS sold FROM order_items oi
             JOIN foods f ON f.id = oi.food_id
             JOIN orders o ON o.id = oi.order_id AND o.status <> 'cancelled'
             GROUP BY f.id ORDER BY sold DESC LIMIT " . $limit
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
