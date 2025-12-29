<?php

/**
 * Universal API Entry Point
 * Developed by Bastivan Consulting
 */

use Bastivan\UniversalApi\Core\Bootstrap;
use Bastivan\UniversalApi\Core\Router;
use Bastivan\UniversalApi\Core\Debugger;
use Bastivan\UniversalApi\Core\Middleware\SecurityMiddleware;
use Bastivan\UniversalApi\Core\Middleware\GzipMiddleware;
use Bastivan\UniversalApi\Controllers\HomeController;

require_once __DIR__ . '/../vendor/autoload.php';

// Initialize System
Bootstrap::init(__DIR__ . '/../');

// Start Error Handling / Debugging
Debugger::registerErrorHandler();

// Run Middleware
SecurityMiddleware::handle();
GzipMiddleware::handle();

// Setup Routing
$router = new Router();

$router->get('/', [HomeController::class, 'index']);
$router->get('/status', [HomeController::class, 'status']);

// Load Custom Hooks
$hookRoutes = __DIR__ . '/../hook/routes.php';
if (file_exists($hookRoutes)) {
    require_once $hookRoutes;
}

// Dispatch
$router->dispatch();
