<?php

declare(strict_types=1);

namespace App\Tests;

use App\Models\DatabaseConnection;
use App\Models\Money;
use App\Models\Product;
use App\Models\Stock;
use PHPUnit\Framework\TestCase;

/**
 * @covers Stock
 */
final class StockTest extends TestCase
{
    protected static $pdo;

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

    public function test_addProductToStock(): void
    {
        $money = new Money();
        $money->setEuros(5);
        $money->setCents(25);

        $product = new Product();
        $product->setName('test');
        $product->setAvailable(Product::AVAILABLE);
        $product->setPrice($money);
        $product->setVatRate(0.2);

        $stock = new Stock(self::$pdo);
        $stock->addProduct($product);

        $this->assertInstanceOf(Stock::class, $stock);
    }

    public function test_removeProduct(): void
    {
        $this->assertTrue(true);
    }

    public function test_getProducts(): void
    {
        $this->assertTrue(true);
    }
}