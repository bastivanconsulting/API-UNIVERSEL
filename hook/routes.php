<?php

/**
 * Custom Routes Hook
 * Developed by Bastivan Consulting
 *
 * Add your custom routes here.
 * Variable $router is available.
 */

use Bastivan\UniversalApi\Core\Router;
use Bastivan\UniversalApi\Hook\Controllers\CustomController;

/** @var Router $router */

// Example of a cached route
$router->get('/custom', [CustomController::class, 'index'], ['cache' => true, 'cache_ttl' => 600]);

// Example of a non-cached route (e.g., authentication)
// $router->post('/login', [AuthController::class, 'login'], ['cache' => false]);

// Example of route with strict method security
$router->get('/secure-data', [CustomController::class, 'index'], [
    'security' => [
        'allowed_methods' => ['GET'] // Explicitly only allow GET (Redundant here but useful for documentation or if route registration changes)
    ]
]);

// Example using other methods
// $router->put('/data/update', [DataController::class, 'update']);
// $router->delete('/data/delete', [DataController::class, 'delete']);
