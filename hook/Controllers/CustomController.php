<?php

declare(strict_types=1);

namespace Bastivan\UniversalApi\Hook\Controllers;

/**
 * Class CustomController
 * Example controller for Hooks
 */
class CustomController
{
    public function index(): void
    {
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Hello from Custom Hook!']);
    }
}
