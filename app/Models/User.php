<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected static string $table = 'users';

    public static function findByEmail(string $email): ?array
    {
        $stmt = static::db()->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public static function withOrderStats(): array
    {
        return static::db()->query(
            "SELECT u.id, u.name, u.email, u.phone, u.role, u.created_at,
                    COUNT(o.id) AS order_count,
                    COALESCE(SUM(CASE WHEN o.status = 'completed' THEN o.total_amount END), 0) AS total_spent
             FROM users u LEFT JOIN orders o ON o.user_id = u.id
             GROUP BY u.id ORDER BY u.id DESC"
        )->fetchAll();
    }

    public static function countCustomers(): int
    {
        return (int) static::db()->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
    }
}
