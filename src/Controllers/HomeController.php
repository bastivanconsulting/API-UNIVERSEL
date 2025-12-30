<?php

declare(strict_types=1);

namespace Bastivan\UniversalApi\Controllers;

use Bastivan\UniversalApi\Core\Debugger;
use Bastivan\UniversalApi\Core\Config;

/**
 * Class HomeController
 * Developed by Bastivan Consulting
 */
class HomeController
{
    public function index(): void
    {
        // Simple View Rendering
        require __DIR__ . '/../Views/home.php';
    }

    public function status(): void
    {
        $dbStatus = 'ok';
        $redisStatus = 'ok';
        $httpCode = 200;

        try {
            \Bastivan\UniversalApi\Core\Database::getInstance()->getConnection()->query('SELECT 1');
        } catch (\Exception $e) {
            $dbStatus = 'error';
            $httpCode = 503;
        }

        try {
            \Bastivan\UniversalApi\Core\Cache::getClient()->ping();
        } catch (\Exception $e) {
            $redisStatus = 'error';
            $httpCode = 503; // Service Unavailable
        }

        http_response_code($httpCode);
        header('Content-Type: application/json');
        echo json_encode([
            'status' => $httpCode === 200 ? 'ok' : 'degraded',
            'dependencies' => [
                'database' => $dbStatus,
                'redis' => $redisStatus
            ],
            'version' => '1.0.0'
        ]);
    }
}
