<?php

declare(strict_types=1);

namespace App\Middlewares;

class ShipperMiddleware
{
    public function handle(): void
    {
        if ((auth()['role'] ?? '') !== 'shipper') {
            http_response_code(403);
            exit('403 - Chỉ nhân viên giao hàng được truy cập.');
        }
    }
}
