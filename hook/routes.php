<?php

/**
 * Custom Routes Definition
 *
 * @var \Bastivan\UniversalApi\Core\Router $router
 */

use Bastivan\UniversalApi\Hook\Controllers\CustomController;

// Example Route
$router->get('/hook/custom', [CustomController::class, 'index']);
