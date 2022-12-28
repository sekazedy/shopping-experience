<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Models\DatabaseConnection;
use PDO;
use PHPUnit\Framework\TestCase;

class BaseTestConfig extends TestCase
{
    protected static ?PDO $pdo;

    public static function setUpBeforeClass(): void
    {
        self::$pdo = (new DatabaseConnection())->getPDO();
        self::$pdo->beginTransaction();
    }

    public static function tearDownAfterClass(): void
    {
        self::$pdo->rollBack();
        self::$pdo = null;
    }
}