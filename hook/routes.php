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

$router->get('/custom', [CustomController::class, 'index']);
