<?php

namespace Bastivan\UniversalApi\Hook\Controllers;

use Bastivan\UniversalApi\Core\Database;

class Web3Controller
{
    private function getUser()
    {
        $headers = getallheaders();
        $token = str_replace('Bearer ', '', $headers['Authorization'] ?? '');
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE session_token = ?");
        $stmt->execute([$token]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function balance()
    {
        $user = $this->getUser();
        if (!$user) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        echo json_encode([
            'user_id' => $user['id'],
            'crypto' => 'BTC',
            'balance' => $user['crypto_balance']
        ]);
    }

    public function location()
    {
        $user = $this->getUser();
        if (!$user) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $method = $_SERVER['REQUEST_METHOD'];
        $db = Database::getInstance()->getConnection();

        if ($method === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            if (isset($data['lat']) && isset($data['lon'])) {
                $stmt = $db->prepare("UPDATE users SET location_lat = ?, location_lon = ? WHERE id = ?");
                $stmt->execute([$data['lat'], $data['lon'], $user['id']]);
                echo json_encode(['message' => 'Location updated']);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Missing lat/lon']);
            }
        } else {
            echo json_encode([
                'lat' => $user['location_lat'],
                'lon' => $user['location_lon']
            ]);
        }
    }
}
