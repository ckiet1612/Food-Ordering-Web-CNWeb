<?php

// Điểm xuất phát demo: Yumio Bách Khoa. Khoảng cách đường chim bay chỉ để tham khảo.
return [
    'name' => 'Yumio Bách Khoa',
    'lat' => (float) (getenv('STORE_LAT') ?: 21.0070),
    'lng' => (float) (getenv('STORE_LNG') ?: 105.8431),
    'tile_url' => getenv('MAP_TILE_URL') ?: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
    'geocoding_enabled' => getenv('GEOCODING_ENABLED') !== '0',
    // Photon hỗ trợ search-as-you-type; không trỏ tới Nominatim công cộng.
    'geocoding_url' => getenv('GEOCODING_URL') ?: 'https://photon.komoot.io/api',
];
