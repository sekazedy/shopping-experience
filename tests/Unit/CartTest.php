<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Exceptions\StockProductNotAvailableException;
use App\Exceptions\StockProductNotFoundException;
use App\Models\Cart;
use App\Models\Money;
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

        $this->assertNotEquals(0, $cart->getId());
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
        $product = Helpers::getNewProduct();
        $product2 = Helpers::getNewProduct();

        $stock = new Stock(self::$pdo);
        $stock->addProduct($product)->addProduct($product2);

        $cart = new Cart(pdo: self::$pdo);
        $cart->addProduct($product)->addProduct($product2);

        $products = $cart->getProducts();

        $this->assertNotEmpty($products);
        $this->assertEquals(2, count($products));
    }

    public function test_getProducts_returnsEmptyArray(): void
    {
        $cart = new Cart(pdo: self::$pdo);

        $products = $cart->getProducts();

        $this->assertEmpty($products);
    }

    public function test_getSubtotal(): void
    {
        $product = Helpers::getNewProduct();
        $product2 = Helpers::getNewProduct();

        $productsCents = bcadd(
            (string)$product->getPrice()->getCents(),
            (string)$product2->getPrice()->getCents(),
            2
        );
        $productsEuros = bcadd(
            (string)$product->getPrice()->getEuros(),
            (string)$product2->getPrice()->getEuros(),
            2
        );

        $money = new Money((int)$productsCents, (int)$productsEuros);

        $subTotal = bcadd(
            $product->getPrice()->getFormattedPrice(),
            $product2->getPrice()->getFormattedPrice(),
            2
        );

        $stock = new Stock(self::$pdo);
        $stock->addProduct($product)->addProduct($product2);

        $cart = new Cart(pdo: self::$pdo);
        $cart->addProduct($product)->addProduct($product2);

        $this->assertEquals($money, $cart->getSubtotal());
        $this->assertEquals($subTotal, $cart->getSubtotal()->getFormattedPrice());
    }

    public function test_getVatAmount(): void
    {
        $product = Helpers::getNewProduct();
        $product2 = Helpers::getNewProduct();

        $product1VatAmount = bcmul(
            $product->getPrice()->getFormattedPrice(),
            (string)$product->getVatRate(),
            2
        );
        $product2VatAmount = bcmul(
            $product2->getPrice()->getFormattedPrice(),
            (string)$product2->getVatRate(),
            2
        );

        $totalVatAmount = bcadd($product1VatAmount, $product2VatAmount, 2);
        $cents = bcsub($totalVatAmount, (string)((int)$totalVatAmount), 2) * 100;
        $euros = $totalVatAmount;

        $money = new Money((int)$cents, (int)$euros);

        $stock = new Stock(self::$pdo);
        $stock->addProduct($product)->addProduct($product2);

        $cart = new Cart(pdo: self::$pdo);
        $cart->addProduct($product)->addProduct($product2);

        $this->assertEquals($money, $cart->getVatAmount());
        $this->assertEquals($totalVatAmount, $cart->getVatAmount()->getFormattedPrice());
    }

    public function test_getTotal(): void
    {
        $product = Helpers::getNewProduct();
        $product2 = Helpers::getNewProduct();

        $product1VatAmount = bcmul(
            $product->getPrice()->getFormattedPrice(),
            (string)$product->getVatRate(),
            2
        );
        $product2VatAmount = bcmul(
            $product2->getPrice()->getFormattedPrice(),
            (string)$product2->getVatRate(),
            2
        );

        $totalVatAmount = bcadd($product1VatAmount, $product2VatAmount, 2);
        $subTotal = bcadd(
            $product->getPrice()->getFormattedPrice(),
            $product2->getPrice()->getFormattedPrice(),
            2
        );

        $total = bcadd($totalVatAmount, $subTotal, 2);

        $cents = bcsub($total, (string)((int)$total), 2) * 100;
        $euros = $total;

        $money = new Money((int)$cents, (int)$euros);

        $stock = new Stock(self::$pdo);
        $stock->addProduct($product)->addProduct($product2);

        $cart = new Cart(pdo: self::$pdo);
        $cart->addProduct($product)->addProduct($product2);

        $this->assertEquals($money, $cart->getTotal());
        $this->assertEquals($total, $cart->getTotal()->getFormattedPrice());
    }
}