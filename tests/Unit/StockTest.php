<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Exceptions\DatabaseErrorException;
use App\Models\Stock;
use App\Tests\Helpers;

use function PHPUnit\Framework\assertEquals;

/**
 * @covers Stock
 */
final class StockTest extends BaseTestConfig
{
    public function test_addProductToStock(): void
    {
        $product = Helpers::getNewProduct();

        $stock = new Stock(self::$pdo);
        $stock->addProduct($product);

        $this->assertNotNull($stock->getId());
    }

    public function test_addSameProductToStockTwice_throwsException(): void
    {
        $this->expectException(DatabaseErrorException::class);
        $this->expectExceptionMessage('Error: duplicate product name found!');

        $product = Helpers::getNewProduct();

        $stock = new Stock(self::$pdo);
        $stock->addProduct($product)->addProduct($product);
    }

    public function test_removeProduct(): void
    {
        $product = Helpers::getNewProduct();

        $stock = new Stock(self::$pdo);
        $stock->addProduct($product);
        $stock->removeProduct($product);

        $this->assertEmpty($stock->getProductDataByName($product->getName()));
    }

    public function test_getProducts(): void
    {
        $product = Helpers::getNewProduct();
        $product2 = Helpers::getNewProduct();

        $stock = new Stock(self::$pdo);
        $stock->addProduct($product)->addProduct($product2);
        $stockProducts = $stock->getProducts();

        $this->assertNotEmpty($stockProducts);
        $this->assertEquals(2, count($stockProducts));
    }
}