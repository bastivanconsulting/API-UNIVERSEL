<?php

namespace Bastivan\UniversalApi\Hook\Controllers;

use Bastivan\UniversalApi\Core\Database;

class AuthController
{
    public function register()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['username']) || !isset($data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Username and password are required']);
            return;
        }

        $db = Database::getInstance()->getConnection();

        // Check if user exists
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$data['username']]);
        if ($stmt->fetch()) {
            http_response_code(409);
            echo json_encode(['error' => 'User already exists']);
            return;
        }

        // Create user
        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
        if ($stmt->execute([$data['username'], $passwordHash])) {
            http_response_code(201);
            echo json_encode(['message' => 'User registered successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Registration failed']);
        }
    }

    public function login()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['username']) || !isset($data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Username and password are required']);
            return;
        }

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT id, password_hash FROM users WHERE username = ?");
        $stmt->execute([$data['username']]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($user && password_verify($data['password'], $user['password_hash'])) {
            // Generate Session Token
            $token = bin2hex(random_bytes(32));

            // Store token in DB
            $update = $db->prepare("UPDATE users SET session_token = ? WHERE id = ?");
            $update->execute([$token, $user['id']]);

            echo json_encode([
                'message' => 'Login successful',
                'token' => $token
            ]);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
        }
    }

    public function me()
    {
        // User is already attached to request by AuthMiddleware usually,
        // but here we demonstrate fetching it or just returning what we found.
        // Assuming AuthMiddleware sets a global or we re-fetch.
        // For this example, let's rely on AuthMiddleware passing it or looking it up via token.

        // Simpler: AuthMiddleware sets static context or we look up token from header again.
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';
        $token = str_replace('Bearer ', '', $authHeader);

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT id, username, crypto_balance, location_lat, location_lon FROM users WHERE session_token = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($user) {
            echo json_encode(['user' => $user]);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
        }
    }
}
