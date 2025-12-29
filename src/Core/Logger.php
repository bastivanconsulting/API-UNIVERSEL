<?php

declare(strict_types=1);

namespace Bastivan\UniversalApi\Core;

use Monolog\Logger as MonoLogger;
use Monolog\Handler\StreamHandler;
use Aws\S3\S3Client;
use Monolog\Handler\BufferHandler;
// Note: Direct S3 Handler for Monolog might need a custom handler or specific package,
// but for simplicity we will simulate local logging or assume a mounted S3 volume (via fuse)
// or implement a simple custom pusher if needed.
// However, the best practice for S3 logs in K8s is writing to stdout/file and having a sidecar (Fluentd) push to S3.
// To satisfy "Storage on S3 or Local" directly in PHP:

/**
 * Class Logger
 * Developed by Bastivan Consulting
 */
class Logger
{
    private static ?MonoLogger $logger = null;

    public static function init(): void
    {
        if (self::$logger === null) {
            self::$logger = new MonoLogger('bastivan_api');

            $logDriver = Config::get('LOG_DRIVER', 'local'); // 'local' or 's3' (via stream wrapper)

            if ($logDriver === 'local') {
                $path = __DIR__ . '/../../logs/app.log';
                self::$logger->pushHandler(new StreamHandler($path, MonoLogger::DEBUG));
            } elseif ($logDriver === 's3') {
                // Register S3 Stream Wrapper
                $client = new S3Client([
                    'version' => 'latest',
                    'region'  => Config::get('AWS_DEFAULT_REGION'),
                    'credentials' => [
                        'key'    => Config::get('AWS_ACCESS_KEY_ID'),
                        'secret' => Config::get('AWS_SECRET_ACCESS_KEY'),
                    ]
                ]);
                $client->registerStreamWrapper();

                $bucket = Config::get('LOG_S3_BUCKET');
                $key = 'logs/app-' . date('Y-m-d') . '.log';
                $path = "s3://{$bucket}/{$key}";

                self::$logger->pushHandler(new StreamHandler($path, MonoLogger::DEBUG));
            }
        }
    }

    public static function log(string $level, string $message, array $context = []): void
    {
        if (self::$logger === null) self::init();
        self::$logger->log($level, $message, $context);
    }
}
