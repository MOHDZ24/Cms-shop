<?php
declare(strict_types=1);

namespace App\Core;

/**
 * موجّه المسارات (Router) — يدعم /product/{id} ونحوها
 */
final class Router
{
    private array $routes = [];

    public function get(string $pattern, string $handler): void
    {
        $this->routes[] = ['GET', $pattern, $handler];
    }

    public function post(string $pattern, string $handler): void
    {
        $this->routes[] = ['POST', $pattern, $handler];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = '/' . trim($path, '/');
        if (BASE_URL !== '' && str_starts_with($path, BASE_URL)) {
            $path = substr($path, strlen(BASE_URL)) ?: '/';
        }
        $path = '/' . trim($path, '/');

        foreach ($this->routes as [$m, $pattern, $handler]) {
            if ($m !== strtoupper($method)) {
                continue;
            }
            $regex = preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $pattern);
            if (preg_match('#^' . $regex . '$#', $path, $matches)) {
                $args = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                [$class, $action] = explode('@', $handler);
                (new $class())->$action(...array_values($args));
                return;
            }
        }

        http_response_code(404);
        echo View::renderToString('errors/404', ['title' => '404'], 'layouts/front');
    }
}
