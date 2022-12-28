<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Models\Stock;
use App\Tests\Helpers;

/**
 * @covers Stock
 */
final class StockTest extends BaseTestConfig
{
    public function test_addProductToStock(): void
    {
        $product = Helpers::getNewProduct(self::$pdo);

        $stock = new Stock(self::$pdo);
        $stock->addProduct($product);

        $this->assertEquals(1, $stock->getQuantity());
    }

    public function test_addSameProductToStockTwice(): void
    {
        $product = Helpers::getNewProduct(self::$pdo);

        $stock = new Stock(self::$pdo);
        $stock->addProduct($product)->addProduct($product);

        $this->assertEquals(2, $stock->getQuantity());
    }

    public function test_removeSingleProduct(): void
    {
        $product = Helpers::getNewProduct(self::$pdo);

        $stock = new Stock(self::$pdo);
        $stock->addProduct($product);
        $stock->removeProduct($product);

        $this->assertEquals(0, $stock->getQuantity());
    }

    public function test_removeOnlyOneOfTwoProducts(): void
    {
        $product = Helpers::getNewProduct(self::$pdo);

        $stock = new Stock(self::$pdo);
        $stock->addProduct($product)->addProduct($product);
        $stock->removeProduct($product);

        $this->assertEquals(1, $stock->getQuantity());
    }

    public function test_removeZeroProducts(): void
    {
        $product = Helpers::getNewProduct(self::$pdo);

        $stock = new Stock(self::$pdo);
        $stock->removeProduct($product);

        $this->assertEquals(0, $stock->getQuantity());
    }

    public function test_getProducts(): void
    {
        $product = Helpers::getNewProduct(self::$pdo);
        $product2 = Helpers::getNewProduct(self::$pdo);

        $stock = new Stock(self::$pdo);
        $stock->addProduct($product)->addProduct($product2);
        $stockProducts = $stock->getProducts();

        $this->assertNotEmpty($stockProducts);
    }
}