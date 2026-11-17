<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Geocoder;

class AddressController extends Controller
{
    public function search(): void
    {
        header('Cache-Control: no-store');
        $query = preg_replace('/\s+/u', ' ', (string) $this->input('q', ''));
        if ($query === null || mb_strlen($query) < 4 || mb_strlen($query) > 255) {
            $this->json(['ok' => false, 'message' => 'Nhập từ 4 đến 255 ký tự địa chỉ.', 'results' => []], 422);
        }
        // Hạn chế mỗi tài khoản/session; cache phía server được dùng chung để giảm gọi dịch vụ.
        $now = time();
        $requests = array_values(array_filter($_SESSION['geocode_requests'] ?? [], fn ($at) => $at > $now - 60));
        if (count($requests) >= 12) {
            header('Retry-After: 60');
            $this->json(['ok' => false, 'message' => 'Bạn tìm hơi nhanh. Chờ một phút hoặc nhập địa chỉ thủ công.', 'results' => [], 'retry_after' => 60], 429);
        }
        $_SESSION['geocode_requests'] = [...$requests, $now];
        session_write_close(); // Không giữ khoá session trong lúc chờ API.
        try {
            $this->json(['ok' => true, 'results' => Geocoder::search($query), 'attribution' => 'Photon / OpenStreetMap']);
        } catch (\RuntimeException $e) {
            $rateLimited = $e->getCode() === 429;
            if ($rateLimited) header('Retry-After: 2');
            $this->json([
                'ok' => false, 'results' => [], 'retry_after' => $rateLimited ? 2 : 0,
                'message' => $rateLimited ? 'Vui lòng đợi một chút rồi nhập tiếp địa chỉ.' : 'Chưa lấy được gợi ý. Bạn vẫn có thể nhập đầy đủ địa chỉ và đặt COD.',
            ], $rateLimited ? 429 : 503);
        }
    }
}
