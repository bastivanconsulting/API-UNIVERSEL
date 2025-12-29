<?php

declare(strict_types=1);

namespace Bastivan\UniversalApi\Core;

use PDO;
use PDOException;

/**
 * Class Database
 * Developed by Bastivan Consulting
 *
 * A Singleton PDO wrapper supporting PostgreSQL and MariaDB.
 * Enforces usage of prepared statements.
 */
class Database
{
    private static ?Database $instance = null;
    private PDO $connection;

    private function __construct()
    {
        $driver = Config::get('DB_CONNECTION');
        $host = Config::get('DB_HOST');
        $port = Config::get('DB_PORT');
        $database = Config::get('DB_DATABASE');
        $username = Config::get('DB_USERNAME');
        $password = Config::get('DB_PASSWORD');

        $dsn = "$driver:host=$host;port=$port;dbname=$database";
        if ($driver === 'mysql') {
            $dsn .= ";charset=utf8mb4";
        }

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false, // Important for security
        ];

        try {
            $this->connection = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            throw new \RuntimeException("Bastivan Consulting API - Database Connection Error: " . $e->getMessage());
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    /**
     * Executes a prepared statement.
     * This is the preferred way to query to avoid SQL Injection.
     *
     * @param string $sql
     * @param array $params
     * @return \PDOStatement
     */
    public function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
