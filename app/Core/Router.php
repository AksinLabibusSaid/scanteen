<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $routes = [];

    public function add(string $method, string $path, callable|array $handler): void
    {
        $path = '/' . trim($path, '/');
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function run(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = '/' . trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

        // Menghilangkan basePath jika aplikasi tidak di root domain
        $basePath = '/scanteen'; // Bisa dinamis dari config
        if (str_starts_with($path, $basePath)) {
            $path = '/' . trim(substr($path, strlen($basePath)), '/');
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $path) {
                $handler = $route['handler'];

                if (is_array($handler)) {
                    [$controllerClass, $action] = $handler;
                    $controller = new $controllerClass();
                    $controller->$action();
                } else {
                    $handler();
                }
                return;
            }
        }

        // 404 Not Found
        http_response_code(404);
        echo "404 - Page Not Found";
    }
}
