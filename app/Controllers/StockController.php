<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\DatabaseErrorException;
use App\Models\Money;
use App\Models\Product;
use App\Models\Stock;

final class StockController extends BaseController
{
    public function addProduct(): void
    {
        $data = $this->getPostParams();

        $price = (string)$data['price'];
        $cents = bcsub($price, (string)((int)$price), 2) * 100;
        $euros = $price;

        $price = new Money((int)$cents, (int)$euros);

        $product = new Product(
            $data['name'],
            $data['available'],
            $price,
            $data['vat_rate']
        );

        $stock = new Stock();
        try {
            $stock->addProduct($product);
        } catch (DatabaseErrorException $e) {
            $this->sendOutput($e->getMessage());
        }

        $this->sendOutput('Product added to stock!');
    }

    public function removeProduct(): void
    {
        $data = $this->getPostParams();

        $product = new Product($data['name']);

        $stock = new Stock();
        try {
            $stock->removeProduct($product);
        } catch (DatabaseErrorException $e) {
            $this->sendOutput($e->getMessage());
        }

        $this->sendOutput('Product removed from stock!');
    }

    public function getProducts(): void
    {
        $stock = new Stock();

        $this->sendOutput($stock->getProducts());
    }
}