<?php

declare(strict_types=1);

namespace App\Models;

class Delivery
{
    /** Toạ độ tuỳ chọn: không có bản đồ vẫn đặt hàng bằng địa chỉ chữ. */
    public static function location(mixed $lat, mixed $lng): array
    {
        if (($lat === null || $lat === '') && ($lng === null || $lng === '')) {
            return ['delivery_lat' => null, 'delivery_lng' => null, 'distance_km' => null];
        }
        if (!is_scalar($lat) || !is_scalar($lng) || !is_numeric($lat) || !is_numeric($lng)
            || (float) $lat < -90 || (float) $lat > 90 || (float) $lng < -180 || (float) $lng > 180) {
            throw new \DomainException('Vị trí không hợp lệ. Hãy chọn lại hoặc bỏ ghim bản đồ.');
        }
        $store = config('delivery');
        $a = sin(deg2rad((float) $lat - $store['lat']) / 2) ** 2
            + cos(deg2rad($store['lat'])) * cos(deg2rad((float) $lat)) * sin(deg2rad((float) $lng - $store['lng']) / 2) ** 2;
        return [
            'delivery_lat' => round((float) $lat, 7),
            'delivery_lng' => round((float) $lng, 7),
            'distance_km' => round(6371 * 2 * asin(sqrt(min(1, max(0, $a)))), 2),
        ];
    }
}
