<?php

namespace Bastivan\UniversalApi\Hook\Middleware;

use Bastivan\UniversalApi\Core\Database;

class AuthMiddleware
{
    public static function handle(string $uri): void
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';

        if (empty($authHeader) || !str_starts_with($authHeader, 'Bearer ')) {
            self::unauthorized();
        }

        $token = str_replace('Bearer ', '', $authHeader);

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT id FROM users WHERE session_token = ?");
        $stmt->execute([$token]);

        if (!$stmt->fetch()) {
            self::unauthorized();
        }
    }

    private static function unauthorized()
    {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized: Invalid or missing token']);
        exit;
    }
}
