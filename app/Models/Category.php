<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Category extends Model
{
    protected static string $table = 'categories';

    public static function ordered(): array
    {
        return static::all('sort_order ASC, id ASC');
    }

    public static function findBySlug(string $slug): ?array
    {
        $stmt = static::db()->prepare('SELECT * FROM categories WHERE slug = ?');
        $stmt->execute([$slug]);
        return $stmt->fetch() ?: null;
    }

    public static function withFoodCount(): array
    {
        return static::db()->query(
            'SELECT c.*, COUNT(f.id) AS food_count FROM categories c
             LEFT JOIN foods f ON f.category_id = c.id
             GROUP BY c.id ORDER BY c.sort_order ASC, c.id ASC'
        )->fetchAll();
    }
}
