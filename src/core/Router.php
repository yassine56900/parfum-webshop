<?php
declare(strict_types=1);

final class Router
{
    /** @var array<string, array<string, callable>> */
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->routes['GET'][$this->normalize($path)] = $handler;
    }

    public function post(string $path, callable $handler): void
    {
        $this->routes['POST'][$this->normalize($path)] = $handler;
    }

    public function dispatch(string $method, string $path): void
    {
        $method = strtoupper($method);
        $path = $this->normalize($path);

        $handler = $this->routes[$method][$path] ?? null;

        if (!is_callable($handler)) {
            http_response_code(404);
            echo "<h1>404 - Pagina niet gevonden</h1>";
            echo "<p>Route: " . htmlspecialchars($method . ' ' . $path) . "</p>";
            exit;
        }

        $handler();
    }

    private function normalize(string $path): string
    {
        $path = '/' . trim($path, '/');
        return $path === '/' ? '/' : $path;
    }
}
