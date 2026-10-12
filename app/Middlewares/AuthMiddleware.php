<?php

declare(strict_types=1);

namespace App\Middlewares;

class AuthMiddleware
{
    public function handle(): void
    {
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
