<?php

declare(strict_types=1);

/**
 * Hook System Bootstrap
 * Developed by Bastivan Consulting
 */

// 1. Register Autoloader for Hooks
spl_autoload_register(function ($class) {
    $prefix = 'Bastivan\\UniversalApi\\Hook\\';
    $baseDir = __DIR__ . '/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// 2. Load Routes
$routesFile = __DIR__ . '/routes.php';
if (file_exists($routesFile)) {
    require_once $routesFile;
}
