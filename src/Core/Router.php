<?php

declare(strict_types=1);

namespace Bastivan\UniversalApi\Core;

/**
 * Class Router
 * Developed by Bastivan Consulting
 *
 * Simple Regex Router.
 */
class Router
{
    private array $routes = [];

    public function get(string $path, callable|array $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    private function add(string $method, string $path, callable|array $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'pattern' => '#^' . $path . '$#',
            'handler' => $handler
        ];
    }

    public function dispatch(): void
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $uri, $matches)) {
                array_shift($matches); // Remove full match

                // Rate Limit
                Middleware\RateLimitMiddleware::handle($uri);

                $handler = $route['handler'];
                if (is_array($handler)) {
                    $controllerName = $handler[0];
                    $action = $handler[1];
                    $controller = new $controllerName();
                    call_user_func_array([$controller, $action], $matches);
                } else {
                    call_user_func_array($handler, $matches);
                }
                return;
            }
        }

        http_response_code(404);
        echo "404 Not Found - Bastivan Consulting API";
    }
}
