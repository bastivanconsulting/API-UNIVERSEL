<?php

declare(strict_types=1);

namespace Bastivan\UniversalApi\Core;

/**
 * Class Debugger
 * Developed by Bastivan Consulting
 *
 * Collects comprehensive debug information.
 */
class Debugger
{
    public static function collect(): array
    {
        // Only run if debug mode is enabled
        if (!filter_var(Config::get('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN)) {
            return [];
        }

        $headers = getallheaders();
        $bodySize = strlen((string)file_get_contents('php://input'));

        return [
            'bastivan_debug' => [
                'client_ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'client_os' => self::getOS($_SERVER['HTTP_USER_AGENT'] ?? ''),
                'request_size_bytes' => $bodySize,
                'php_version' => PHP_VERSION,
                'memory_usage' => memory_get_usage(true),
                'status_code' => http_response_code(),
                'timestamp' => microtime(true),
            ]
        ];
    }

    private static function getOS(string $userAgent): string
    {
        if (preg_match('/linux/i', $userAgent)) return 'Linux';
        if (preg_match('/macintosh|mac os x/i', $userAgent)) return 'macOS';
        if (preg_match('/windows|win32/i', $userAgent)) return 'Windows';
        return 'Unknown';
    }

    public static function registerErrorHandler(): void
    {
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            $errorData = [
                'type' => 'PHP Warning/Error',
                'message' => $errstr,
                'file' => $errfile,
                'line' => $errline
            ];

            // Log it
            Logger::log('warning', $errstr, $errorData);

            // If debug is on, we might want to collect it for the response (if json)
            // Storing in a global or static for final output injection
            $GLOBALS['BASTIVAN_DEBUG_ERRORS'][] = $errorData;

            // Return false to let standard handler run if needed, but usually we handle it.
            return false;
        });
    }
}
