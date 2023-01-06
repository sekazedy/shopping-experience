<?php

declare(strict_types=1);

namespace App\Models;

require_once(dirname(__DIR__, 2) . '/bootstrap.php');

use PDO;

final class DatabaseConnection
{
    private PDO $pdo;

    public function __construct()
    {
        // Connect to the database using PDO
        $this->pdo = new PDO(
            "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']}",
            $_ENV['DB_USER'],
            $_ENV['DB_PASSWORD'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }

    /**
     * @return PDO
     */
    public function getPDO(): PDO
    {
        return $this->pdo;
    }
}