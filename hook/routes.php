<?php

/**
 * Custom Routes Hook
 * Developed by Bastivan Consulting
 *
 * Add your custom routes here.
 * Variable $router is available.
 */

use Bastivan\UniversalApi\Core\Router;

/** @var Router $router */

use Bastivan\UniversalApi\Hook\Controllers\CustomController;

// Example of a cached route
$router->get('/custom', [CustomController::class, 'index'], ['cache' => true, 'cache_ttl' => 600]);

// Example of a non-cached route (e.g., authentication)
// $router->post('/login', [AuthController::class, 'login'], ['cache' => false]);

// Example: Get authenticated user details
$router->get('/user/me', [Bastivan\UniversalApi\Hook\Controllers\UserController::class, 'me'], ['cache' => false]);
