<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\CartNotFoundException;
use App\Exceptions\StockProductNotAvailableException;
use App\Exceptions\StockProductNotFoundException;
use App\Exceptions\StockProductNotInCartException;
use App\Models\Cart;
use App\Models\Product;

final class CartController extends BaseController
{
    public function create(): void
    {
        $cart = new Cart();
        $cart->create();

        $this->sendOutput(['cart_id' => $cart->getId()]);
    }

    public function addProduct(): void
    {
        $data = $this->getPostParams();

        $product = new Product($data['name']);

        $cart = new Cart((int)$data['cart_id']);
        try {
            $cart->addProduct($product);
        } catch (StockProductNotFoundException|StockProductNotAvailableException|CartNotFoundException $e) {
            $this->sendOutput($e->getMessage());
        }

        $this->sendOutput('Product added to cart!');
    }

    public function removeProduct(): void
    {
        $data = $this->getPostParams();

        $product = new Product($data['name']);

        $cart = new Cart((int)$data['cart_id']);
        try {
            $cart->removeProduct($product);
        } catch (StockProductNotFoundException|StockProductNotInCartException|CartNotFoundException $e) {
            $this->sendOutput($e->getMessage());
        }

        $this->sendOutput('Product removed from cart!');
    }

    public function getSubtotal(int $cartId): void
    {
        $cart = new Cart($cartId);
        try {
            $subTotal = $cart->getSubtotal()->getFormattedPrice();
        } catch (CartNotFoundException $e) {
            $this->sendOutput($e->getMessage());
        }

        $this->sendOutput([
            'cart_id' => $cartId,
            'subtotal' => $subTotal,
        ]);
    }

    public function getVatAmount(int $cartId): void
    {
        $cart = new Cart($cartId);
        try {
            $vatAmount = $cart->getVatAmount()->getFormattedPrice();
        } catch (CartNotFoundException $e) {
            $this->sendOutput($e->getMessage());
        }

        $this->sendOutput([
            'cart_id' => $cartId,
            'vat_amount' => $vatAmount,
        ]);
    }

    public function getTotal(int $cartId): void
    {
        $cart = new Cart($cartId);
        try {
            $total = $cart->getTotal()->getFormattedPrice();
        } catch (CartNotFoundException $e) {
            $this->sendOutput($e->getMessage());
        }

        $this->sendOutput([
            'cart_id' => $cartId,
            'total' => $total,
        ]);
    }
}