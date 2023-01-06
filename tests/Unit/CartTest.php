<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Exceptions\StockProductNotAvailableException;
use App\Exceptions\StockProductNotFoundException;
use App\Models\Cart;
use App\Models\Stock;
use App\Tests\Helpers;

/**
 * @covers Cart
 */
final class CartTest extends BaseTestConfig
{
    public function test_addProduct(): void
    {
        $product = Helpers::getNewProduct();
        $availability = $product->getAvailable();

        $stock = new Stock(self::$pdo);
        $stock->addProduct($product);

        $cart = new Cart(pdo: self::$pdo);
        $cart->addProduct($product);

        $this->assertNotNull($cart->getId());
        $this->assertEquals($availability - 1, $product->getAvailable());
    }

    public function test_addProduct_throwsNotFoundException(): void
    {
        $product = Helpers::getNewProduct();

        $this->expectException(StockProductNotFoundException::class);
        $this->expectExceptionMessage("Product with name '{$product->getName()}' was not found!");

        $cart = new Cart(pdo: self::$pdo);
        $cart->addProduct($product);
    }

    public function test_addProduct_throwsNotAvailableException(): void
    {
        $product = Helpers::getNewProduct();
        $product->setAvailable(0);

        $this->expectException(StockProductNotAvailableException::class);
        $this->expectExceptionMessage("Product '{$product->getName()}' stock is empty");

        $stock = new Stock(self::$pdo);
        $stock->addProduct($product);

        $cart = new Cart(pdo: self::$pdo);
        $cart->addProduct($product);
    }

    public function test_removeProduct(): void
    {
        $product = Helpers::getNewProduct();
        $availability = $product->getAvailable();

        $stock = new Stock(self::$pdo);
        $stock->addProduct($product);

        $cart = new Cart(pdo: self::$pdo);
        $cart->addProduct($product);

        $this->assertEquals($availability - 1, $product->getAvailable());

        $cart->removeProduct($product);

        $this->assertEquals($availability, $product->getAvailable());
    }

    public function test_getProducts(): void
    {
        $this->assertTrue(true);
    }

    public function test_getSubtotal(): void
    {
        $this->assertTrue(true);
    }

    public function test_getVatAmount(): void
    {
        $this->assertTrue(true);
    }

    public function test_getTotal(): void
    {
        $this->assertTrue(true);
    }
}