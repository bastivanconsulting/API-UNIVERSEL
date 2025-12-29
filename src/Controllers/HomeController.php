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
        header('Content-Type: application/json');

        $response = [
            'status' => 'ok',
            'app' => 'Universal API',
            'developer' => 'Bastivan Consulting',
            'version' => '1.0.0'
        ];

        if (filter_var(Config::get('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN)) {
            $response = array_merge($response, Debugger::collect());
            if (!empty($GLOBALS['BASTIVAN_DEBUG_ERRORS'])) {
                $response['errors'] = $GLOBALS['BASTIVAN_DEBUG_ERRORS'];
            }
        }

        echo json_encode($response);
    }
}
