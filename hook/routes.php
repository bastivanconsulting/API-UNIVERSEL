<?php

/**
 * Custom Routes Definition
 *
 * @var \Bastivan\UniversalApi\Core\Router $router
 */

use Bastivan\UniversalApi\Hook\Controllers\AuthController;
use Bastivan\UniversalApi\Hook\Controllers\Web3Controller;
use Bastivan\UniversalApi\Hook\Controllers\ShopController;
use Bastivan\UniversalApi\Hook\Middleware\AuthMiddleware;

// User Auth
$router->post('/api/auth/register', [AuthController::class, 'register']);
$router->post('/api/auth/login', [AuthController::class, 'login']);

// User Profile (Protected)
$router->get('/api/user/me', [AuthController::class, 'me'], [
    'middleware' => [AuthMiddleware::class]
]);

// Web3 Features (Protected)
$router->get('/api/web3/balance', [Web3Controller::class, 'balance'], [
    'middleware' => [AuthMiddleware::class]
]);
$router->match(['GET', 'POST'], '/api/web3/location', [Web3Controller::class, 'location'], [
    'middleware' => [AuthMiddleware::class]
]);

// Shop (Public)
$router->get('/api/shop/products', [ShopController::class, 'products']);
