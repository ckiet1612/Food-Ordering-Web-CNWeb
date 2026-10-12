<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array $action, array $middlewares = []): void
    {
        $this->add('GET', $path, $action, $middlewares);
    }

    public function post(string $path, array $action, array $middlewares = []): void
    {
        $this->add('POST', $path, $action, $middlewares);
    }

    private function add(string $method, string $path, array $action, array $middlewares): void
    {
        // "/foods/{slug}" -> "#^/foods/(?P<slug>[^/]+)$#"
        $pattern = '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', rtrim($path, '/') ?: '/') . '$#';

        $this->routes[$method][] = compact('pattern', 'action', 'middlewares');
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = $this->normalizePath($uri);

        foreach ($this->routes[$method] ?? [] as $route) {
            if (!preg_match($route['pattern'], $path, $matches)) {
                continue;
            }

            foreach ($route['middlewares'] as $middleware) {
                (new $middleware())->handle();
            }

            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            [$controller, $action] = $route['action'];
            (new $controller())->$action(...$params);
            return;
        }

        http_response_code(404);
        View::render('errors/404', [], 'main');
    }

    // Bỏ query string và tiền tố thư mục con (nếu app chạy trong htdocs/<project>)
    private function normalizePath(string $uri): string
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $basePath = rtrim(parse_url(config('app')['url'], PHP_URL_PATH) ?? '', '/');

        if ($basePath !== '' && str_starts_with($path, $basePath)) {
            $path = substr($path, strlen($basePath));
        }

        return rtrim($path, '/') ?: '/';
    }
}
