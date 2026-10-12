<?php

declare(strict_types=1);

namespace App\Core;

class View
{
    // Render view bên trong layout; layout nhận nội dung qua biến $content
    public static function render(string $view, array $data = [], ?string $layout = 'main'): void
    {
        $content = self::capture('views/' . $view, $data);

        if ($layout === null) {
            echo $content;
            return;
        }

        echo self::capture('views/layouts/' . $layout, $data + ['content' => $content]);
    }

    private static function capture(string $file, array $data): string
    {
        $path = BASE_PATH . '/app/' . str_replace('views/', 'Views/', $file) . '.php';
        if (!is_file($path)) {
            throw new \RuntimeException("View not found: {$file}");
        }

        extract($data, EXTR_SKIP);
        ob_start();
        require $path;
        return ob_get_clean();
    }
}
