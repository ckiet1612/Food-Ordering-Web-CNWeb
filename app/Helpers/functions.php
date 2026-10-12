<?php

declare(strict_types=1);

function config(string $name): array
{
    static $cache = [];
    return $cache[$name] ??= require BASE_PATH . "/config/{$name}.php";
}

// Escape dữ liệu khi in ra HTML để chống XSS
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function url(string $path = ''): string
{
    return config('app')['url'] . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function auth(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_admin(): bool
{
    return (auth()['role'] ?? null) === 'admin';
}

// Flash message: set khi có tham số $message, lấy (và xoá) khi không có
function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }

    $value = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $value;
}

function csrf_token(): string
{
    return $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function format_price(float|int|string $price): string
{
    return number_format((float) $price, 0, ',', '.') . '₫';
}

// Lỗi validate và dữ liệu cũ của form (chỉ tồn tại trong 1 request sau redirect)
function form_state(): array
{
    static $state = null;

    if ($state === null) {
        $state = ['errors' => $_SESSION['_errors'] ?? [], 'old' => $_SESSION['_old'] ?? []];
        unset($_SESSION['_errors'], $_SESSION['_old']);
    }

    return $state;
}

function error(string $field): ?string
{
    return form_state()['errors'][$field] ?? null;
}

function old(string $field, mixed $default = ''): mixed
{
    return form_state()['old'][$field] ?? $default;
}

// Ảnh lưu dạng đường dẫn tính từ public/ (VD: assets/images/foods/x.png, uploads/foods/y.jpg)
function image_url(?string $path): string
{
    return $path ? url($path) : asset('images/placeholder.svg');
}

function slugify(string $text): string
{
    $map = [
        'a' => 'àáạảãâầấậẩẫăằắặẳẵ', 'e' => 'èéẹẻẽêềếệểễ', 'i' => 'ìíịỉĩ',
        'o' => 'òóọỏõôồốộổỗơờớợởỡ', 'u' => 'ùúụủũưừứựửữ', 'y' => 'ỳýỵỷỹ', 'd' => 'đ',
    ];

    $text = mb_strtolower($text);
    foreach ($map as $ascii => $chars) {
        $text = preg_replace('/[' . $chars . ']/u', $ascii, $text);
    }

    return trim(preg_replace('/[^a-z0-9]+/', '-', $text), '-');
}

function current_path(): string
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $base = rtrim(parse_url(config('app')['url'], PHP_URL_PATH) ?? '', '/');
    return rtrim(substr($path, strlen($base)), '/') ?: '/';
}

function is_active(string $prefix): bool
{
    return $prefix === '/' ? current_path() === '/' : str_starts_with(current_path(), $prefix);
}

// Icon SVG inline (nét 1.5px, kế thừa màu chữ)
function icon(string $name, int $size = 20): string
{
    $paths = [
        'search' => '<circle cx="11" cy="11" r="6.5"/><path d="m20 20-4.2-4.2"/>',
        'pin'    => '<path d="M12 21s-6.5-5.6-6.5-11a6.5 6.5 0 0 1 13 0c0 5.4-6.5 11-6.5 11Z"/><circle cx="12" cy="10" r="2.3"/>',
        'user'   => '<circle cx="12" cy="8.5" r="3.8"/><path d="M4.5 20.5c1.3-3.6 4.2-5.4 7.5-5.4s6.2 1.8 7.5 5.4"/>',
        'bag'    => '<path d="M5 8h14l-1.1 11.2a1.5 1.5 0 0 1-1.5 1.3H7.6a1.5 1.5 0 0 1-1.5-1.3L5 8Z"/><path d="M9 10V7a3 3 0 0 1 6 0v3"/>',
        'menu'   => '<path d="M4 7h16M4 12h16M4 17h16"/>',
        'close'  => '<path d="m6 6 12 12M18 6 6 18"/>',
        'left'   => '<path d="M19 12H5m6-6-6 6 6 6"/>',
        'right'  => '<path d="M5 12h14m-6-6 6 6-6 6"/>',
        'plus'   => '<path d="M12 5v14M5 12h14"/>',
        'minus'  => '<path d="M5 12h14"/>',
        'trash'  => '<path d="M5 7h14M10 11v6M14 11v6M6.5 7l.8 12a1.5 1.5 0 0 0 1.5 1.4h6.4a1.5 1.5 0 0 0 1.5-1.4l.8-12M9 7V5.5A1.5 1.5 0 0 1 10.5 4h3A1.5 1.5 0 0 1 15 5.5V7"/>',
        'check'  => '<path d="m5 12.5 4.5 4.5L19 7.5"/>',
        'clock'  => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/>',
        'phone'  => '<path d="M6.6 3.5h2.6l1.4 4.2-2 1.4a12 12 0 0 0 6.3 6.3l1.4-2 4.2 1.4v2.6a2 2 0 0 1-2.2 2A16.5 16.5 0 0 1 4.6 5.7a2 2 0 0 1 2-2.2Z"/>',
        'truck'  => '<path d="M3 6.5h11v9H3zM14 9.5h3.8l3.2 3.3v2.7h-7"/><circle cx="7" cy="17.5" r="1.8"/><circle cx="17.5" cy="17.5" r="1.8"/>',
        'logout' => '<path d="M14 4.5h3.5A1.5 1.5 0 0 1 19 6v12a1.5 1.5 0 0 1-1.5 1.5H14M10 16l-4-4 4-4M6 12h9"/>',
        'grid'   => '<rect x="4" y="4" width="6.5" height="6.5" rx="1.5"/><rect x="13.5" y="4" width="6.5" height="6.5" rx="1.5"/><rect x="4" y="13.5" width="6.5" height="6.5" rx="1.5"/><rect x="13.5" y="13.5" width="6.5" height="6.5" rx="1.5"/>',
        'receipt'=> '<path d="M6 3.5h12v17l-3-2-3 2-3-2-3 2v-17Z"/><path d="M9 8.5h6M9 12h6"/>',
        'users'  => '<circle cx="9" cy="8.5" r="3.3"/><path d="M3 19.5c1-3 3.3-4.6 6-4.6s5 1.6 6 4.6"/><path d="M15.5 5.5a3.3 3.3 0 0 1 0 6.3M17.5 15.2c1.7.6 2.9 2 3.5 4.3"/>',
        'tag'    => '<path d="M3.5 12.6V4.5a1 1 0 0 1 1-1h8.1l8 8a1.4 1.4 0 0 1 0 2l-7.1 7.1a1.4 1.4 0 0 1-2 0l-8-8Z"/><circle cx="8" cy="8" r="1.4"/>',
        'home'   => '<path d="M4 10.5 12 4l8 6.5V19a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 4 19v-8.5Z"/><path d="M9.5 20.5v-6h5v6"/>',
        'edit'   => '<path d="M14.5 5.5 18.5 9.5M4 20l1-4.5L15.8 4.7a1.4 1.4 0 0 1 2 0l1.5 1.5a1.4 1.4 0 0 1 0 2L8.5 19 4 20Z"/>',
    ];

    return sprintf(
        '<svg class="icon" width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">%2$s</svg>',
        $size,
        $paths[$name] ?? ''
    );
}
