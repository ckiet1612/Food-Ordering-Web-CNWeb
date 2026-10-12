<?php

declare(strict_types=1);

namespace App\Middlewares;

class AdminMiddleware
{
    public function handle(): void
    {
        if (!is_admin()) {
            http_response_code(403);
            exit('403 - Bạn không có quyền truy cập trang này.');
        }
    }
}
