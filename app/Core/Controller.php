<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function view(string $view, array $data = [], ?string $layout = 'main'): void
    {
        View::render($view, $data, $layout);
    }

    protected function json(mixed $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function redirect(string $path): never
    {
        redirect($path);
    }

    protected function input(string $key, mixed $default = null): mixed
    {
        $value = $_POST[$key] ?? $_GET[$key] ?? $default;
        return is_string($value) ? trim($value) : $value;
    }

    protected function wantsJson(): bool
    {
        return str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
    }

    // Kiểm tra dữ liệu POST; nếu lỗi thì quay lại $backTo kèm lỗi + dữ liệu cũ
    protected function validate(array $rules, array $labels, string $backTo): array
    {
        $errors = Validator::validate($_POST, $rules, $labels);

        if ($errors !== []) {
            $_SESSION['_errors'] = $errors;
            $_SESSION['_old'] = array_diff_key($_POST, array_flip(['password', 'password_confirmation', 'csrf_token']));
            $this->redirect($backTo);
        }

        return array_map(fn ($v) => is_string($v) ? trim($v) : $v, array_intersect_key($_POST, $rules));
    }

    protected function notFound(): never
    {
        http_response_code(404);
        $this->view('errors/404', ['title' => 'Không tìm thấy trang']);
        exit;
    }
}
