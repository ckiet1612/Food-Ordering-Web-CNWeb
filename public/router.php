<?php

declare(strict_types=1);

// Router cho PHP built-in server; chỉ public/ được phép phục vụ file tĩnh.
if (PHP_SAPI === 'cli-server') {
    $path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
    $file = realpath(__DIR__ . $path);
    if ($file !== false && str_starts_with($file, __DIR__ . DIRECTORY_SEPARATOR)
        && is_file($file) && preg_match('/\.(?:css|js|png|jpe?g|webp|svg|ico|woff2?|map)$/i', $file)) {
        return false;
    }
}

require __DIR__ . '/index.php';
