<?php

declare(strict_types=1);

namespace App\Middlewares;

class CsrfMiddleware
{
    public function handle(): void
    {
        if (!hash_equals(csrf_token(), $_POST['csrf_token'] ?? '')) {
            http_response_code(419);
            exit('419 - Phiên làm việc đã hết hạn, vui lòng tải lại trang.');
        }
    }
}
