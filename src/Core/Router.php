<?php

declare(strict_types=1);

namespace Bastivan\UniversalApi\Core;
use Bastivan\UniversalApi\Controllers\ErrorController;

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

                // Rate Limit
                Middleware\RateLimitMiddleware::handle($uri);

                // Check Cache
                $cacheEnabled = $route['options']['cache'] ?? false;
                $cacheTTL = (int)($route['options']['cache_ttl'] ?? 3600);
                $cacheKey = 'route_cache:' . md5($method . $uri);

                if ($cacheEnabled) {
                    try {
                        $cachedData = Cache::get($cacheKey);
                        if ($cachedData !== null) {
                            $response = json_decode($cachedData, true);
                            if (json_last_error() === JSON_ERROR_NONE && isset($response['body'])) {
                                // Replay Status Code
                                if (isset($response['code'])) {
                                    http_response_code($response['code']);
                                }
                                // Replay Headers
                                if (isset($response['headers']) && is_array($response['headers'])) {
                                    foreach ($response['headers'] as $header) {
                                        header($header);
                                    }
                                }
                                echo $response['body'];
                                return;
                            }
                        }
                    } catch (\Exception $e) {
                        // Proceed without cache on error
                    }
                }

                if ($cacheEnabled) {
                    ob_start();
                }

                try {
                    $handler = $route['handler'];
                    if (is_array($handler)) {
                        $controllerName = $handler[0];
                        $action = $handler[1];
                        $controller = new $controllerName();
                        call_user_func_array([$controller, $action], $matches);
                    } else {
                        call_user_func_array($handler, $matches);
                    }
                } catch (\Throwable $e) {
                    if ($cacheEnabled) {
                        ob_end_clean(); // Discard buffer on error
                    }
                    throw $e; // Re-throw
                }

                if ($cacheEnabled) {
                    $content = ob_get_clean();
                    if ($content !== false) {
                        try {
                            $headers = headers_list();
                            $code = http_response_code();

                            $cachePayload = json_encode([
                                'code' => $code,
                                'headers' => $headers,
                                'body' => $content
                            ]);

                            if ($cachePayload !== false) {
                                Cache::set($cacheKey, $cachePayload, $cacheTTL);
                            }
                        } catch (\Exception $e) {
                            // Ignore cache write errors
                        }
                        echo $content;
                    }
                }

                return;
            }
        }

        // 404
        $controller = new ErrorController();
        $controller->notFound(['message' => "Route not found: $uri"]);
    }
}
