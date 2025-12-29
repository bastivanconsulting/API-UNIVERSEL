<?php

namespace Bastivan\UniversalApi\Hook\Middleware;

use Bastivan\UniversalApi\Core\Database;

class AuthMiddleware
{
    private static ?array $currentUser = null;

    public static function check(): void
    {
        $headers = getallheaders();
        $token = null;

        $authHeader = $headers['Authorization'] ?? '';

        // Support "Bearer <token>"
        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
             $token = $matches[1];
        } else {
             // Fallback: Use the whole header if no Bearer prefix (or handle as needed)
             $token = $authHeader ?: null;
        }

        if (!$token) {
            self::abort();
        }

        $db = Database::getInstance();

        // This query assumes a specific table structure (api_tokens).
        // For the documentation example to work, the user needs to create this table.
        // We will document this requirement in the README.
        $stmt = $db->query("SELECT user_id FROM api_tokens WHERE token = ?", [$token]);
        $tokenData = $stmt->fetch();

        if (!$tokenData) {
            self::abort();
        }

        // Fetch user details
        $stmtUser = $db->query("SELECT id, name, email FROM users WHERE id = ?", [$tokenData['user_id']]);
        $user = $stmtUser->fetch();

        if (!$user) {
            self::abort();
        }

        self::$currentUser = $user;
    }

    public static function user(): ?array
    {
        return self::$currentUser;
    }

    private static function abort(): void
    {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => '403 Forbidden - Invalid or missing token']);
        exit;
    }
}
