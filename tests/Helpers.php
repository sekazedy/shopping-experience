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

        $money = new Money($cents, $euros);

        $product = new Product(
            'test_' . $euros . '_' . $cents,
            Product::AVAILABLE,
            $money,
            $vat,
            pdo: $pdo
        );

        return $product->create();
    }
}