<?php

declare(strict_types=1);

namespace App\Tests;

use App\Models\Money;
use App\Models\Product;

class Helpers
{
    public static function getNewProduct(): Product
    {
        $euros = rand(0, 200);
        $cents = rand(0, 99);
        $vatRate = rand(0, 100) * 0.01;

        $money = new Money($cents, $euros);

        return new Product(
            'test_' . $euros . '_' . $cents,
            5,
            $money,
            $vatRate
        );
    }
}