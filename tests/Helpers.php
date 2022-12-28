<?php

declare(strict_types=1);

namespace App\Tests;

use App\Models\Money;
use App\Models\Product;
use PDO;

class Helpers
{
    public static function getNewProduct(PDO $pdo): Product
    {
        $euros = rand(0, 200);
        $cents = rand(0, 100);
        $vat = rand(0, 100) * 0.01;

        $money = new Money();
        $money->setEuros($euros);
        $money->setCents($cents);

        $product = new Product($pdo);
        $product->setName('test_' . $euros . '_' . $cents);
        $product->setAvailable(Product::AVAILABLE);
        $product->setPrice($money);
        $product->setVatRate($vat);

        return $product->create();
    }
}