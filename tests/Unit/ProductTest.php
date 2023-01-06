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
        $unitsAvailable = 7;

        $product = new Product();
        $product->setAvailable($unitsAvailable);

        $this->assertEquals($unitsAvailable, $product->getAvailable());
    }

    public function test_getAvailable(): void
    {
        $unitsAvailable = 12;

        $product = new Product(available: $unitsAvailable);

        $this->assertEquals($unitsAvailable, $product->getAvailable());
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
        $vatRate = 0.32;

        $product = new Product();
        $product->setVatRate($vatRate);

        $this->assertEquals($vatRate, $product->getVatRate());
    }

    public function test_getVatRate(): void
    {
        $vatRate = 0.45;

        $product = new Product(vatRate: $vatRate);

        $this->assertEquals($vatRate, $product->getVatRate());
    }
}