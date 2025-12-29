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

    public function get(string $path, callable|array $handler, array $options = []): void
    {
        $this->add('GET', $path, $handler, $options);
    }

    public function post(string $path, callable|array $handler, array $options = []): void
    {
        $this->add('POST', $path, $handler, $options);
    }

    private function add(string $method, string $path, callable|array $handler, array $options = []): void
    {
        $this->routes[] = [
            'method' => $method,
            'pattern' => '#^' . $path . '$#',
            'handler' => $handler,
            'options' => $options
        ];
    }

    public function dispatch(): void
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $uri, $matches)) {
                array_shift($matches); // Remove full match

                // Security Check
                if (isset($route['options']['security'])) {
                    Middleware\SecurityMiddleware::checkRouteSecurity($route['options']['security']);
                }

                // Rate Limit
                Middleware\RateLimitMiddleware::handle($uri);

                // Cache Check
                $cacheEnabled = isset($route['options']['cache']) && is_int($route['options']['cache']);
                if ($cacheEnabled) {
                    $cachedResponse = Middleware\CacheMiddleware::check($uri);
                    if ($cachedResponse) {
                        http_response_code($cachedResponse['statusCode']);
                        foreach ($cachedResponse['headers'] as $header) {
                            header($header);
                        }
                        echo $cachedResponse['content'];
                        return;
                    }
                    ob_start();
                }

                $handler = $route['handler'];
                if (is_array($handler)) {
                    $controllerName = $handler[0];
                    $action = $handler[1];
                    $controller = new $controllerName();
                    call_user_func_array([$controller, $action], $matches);
                } else {
                    call_user_func_array($handler, $matches);
                }

                if ($cacheEnabled) {
                    $content = ob_get_contents();
                    ob_end_flush();
                    Middleware\CacheMiddleware::store($uri, $content, $route['options']['cache']);
                }
                return;
            }
        }

        http_response_code(404);
        echo "404 Not Found - Bastivan Consulting API";
    }
}
