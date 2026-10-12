<?php

return [
    'name'     => 'Yumio',
    // URL gốc của website, không có dấu "/" ở cuối.
    // VD: http://localhost:8000 hoặc http://localhost/Food-Ordering-Web-CNWeb
    'url'      => getenv('APP_URL') ?: 'http://localhost:8000',
    'debug'    => true,
    'timezone' => 'Asia/Ho_Chi_Minh',
];
