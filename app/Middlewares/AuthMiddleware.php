<?php

declare(strict_types=1);

namespace App\Middlewares;

class AuthMiddleware
{
    public function handle(): void
    {
        if (isset($_SESSION['user']['id'])) {
            $user = \App\Models\User::find((int) $_SESSION['user']['id']);
            if ($user !== null) {
                unset($user['password']);
                $_SESSION['user'] = $user;
            } else {
                unset($_SESSION['user']);
            }
        }
        if (auth() === null) {
            // Ghi nhớ trang đang mở để quay lại sau khi đăng nhập
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $_SESSION['intended'] = current_path();
            }
            flash('error', 'Vui lòng đăng nhập để tiếp tục.');
            redirect('/login');
        }
    }
}
