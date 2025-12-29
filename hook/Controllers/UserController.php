<?php

namespace Bastivan\UniversalApi\Hook\Controllers;

use Bastivan\UniversalApi\Hook\Middleware\AuthMiddleware;

class UserController
{
    public function me()
    {
        // Enforce authentication
        AuthMiddleware::check();

        // Get the authenticated user
        $user = AuthMiddleware::user();

        header('Content-Type: application/json');
        echo json_encode($user);
    }
}
