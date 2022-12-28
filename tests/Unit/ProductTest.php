<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Models\Money;
use App\Models\Product;

/**
 * @covers Product
 */
final class ProductTest extends BaseTestConfig
{
    public function test_setName(): void
    {
        $productName = 'product_1';
        $product = new Product();
        $product->setName($productName);

        $this->assertEquals($productName, $product->getName());
    }

    public function test_getName(): void
    {
        $productName = 'product_2';
        $product = new Product($productName);

        $this->assertEquals($productName, $product->getName());
    }

    public function test_setAvailable(): void
    {
        $product = new Product();
        $product->setAvailable(Product::AVAILABLE);

        $this->assertEquals(Product::AVAILABLE, $product->getAvailable());
    }

    public function test_getAvailable(): void
    {
        $product = new Product(available: Product::AVAILABLE);

        $this->assertEquals(Product::AVAILABLE, $product->getAvailable());
    }

    public function test_setPrice(): void
    {
        $product = new Product();
        $product->setPrice(new Money(11, 50));

        $this->assertInstanceOf(Money::class, $product->getPrice());
    }

    public function test_getPrice(): void
    {
        $product = new Product(price: new Money(11, 50));

        $this->assertInstanceOf(Money::class, $product->getPrice());
    }

    public function test_setVatRate(): void
    {
        $vat = 0.32;

        $product = new Product();
        $product->setVatRate($vat);

        $this->assertEquals($vat, $product->getVatRate());
    }

    public function test_getVatRate(): void
    {
        $vat = 0.45;

        $product = new Product(vat: $vat);

        $this->assertEquals($vat, $product->getVatRate());
    }

    public function test_createNewProduct(): void
    {
        $money = new Money();
        $money->setEuros(22);
        $money->setCents(56);

        $product = new Product(pdo: self::$pdo);
        $product->setName('created product 1');
        $product->setAvailable(Product::AVAILABLE);
        $product->setPrice($money);
        $product->setVatRate(0.15);
        $product->create();

        $this->assertNotNull($product->getId());
    }
}