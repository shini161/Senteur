<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use RuntimeException;

/**
 * Creates a PDO connection using environment variables.
 * Uses a singleton-style static connection reused within the request.
 */
class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $host = self::getEnv('DB_HOST');
            $port = self::getEnv('DB_PORT');
            $db   = self::getEnv('DB_DATABASE');
            $user = self::getEnv('DB_USERNAME');
            $pass = $_ENV['DB_PASSWORD'] ?? '';

            $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

            self::$connection = new PDO(
                $dsn,
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ]
            );
        }

        return self::$connection;
    }

    private static function getEnv(string $key): string
    {
        $value = $_ENV[$key] ?? null;

        if ($value === null || $value === '') {
            throw new RuntimeException("Missing required environment variable: {$key}");
        }

        return (string) $value;
    }
}
