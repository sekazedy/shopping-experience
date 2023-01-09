<?php

use App\Controllers\CartController;
use App\Controllers\StockController;

function route($method, $url, $callback) {
    $method = strtoupper($method);
    if (isset($_SERVER['REQUEST_METHOD'])
        && $_SERVER['REQUEST_METHOD'] === $method
        && preg_match("`^$url$`i", $_SERVER['REQUEST_URI'], $matches)
    ) {
        array_shift($matches);
        $callback(...$matches);
    }
}

/**
 * Test if the routes are working
 */
route('GET', '/test', function () {
    echo 'Hello, World!!!';
});

route('GET', '/stock/get-products', function () {
    (new StockController())->getProducts();
});

route('POST', '/stock/add-product', function () {
    (new StockController())->addProduct();
});

route('DELETE', '/stock/remove-product', function () {
    (new StockController())->removeProduct();
});

route('POST', '/cart/create', function () {
    (new CartController())->create();
});

route('POST', '/cart/add-product', function () {
    (new CartController())->addProduct();
});

route('POST', '/cart/remove-product', function () {
    (new CartController())->removeProduct();
});

route('GET', '/cart/([0-9]+)/get-subtotal', function (int $cartId) {
    (new CartController())->getSubtotal($cartId);
});

route('GET', '/cart/([0-9]+)/get-vat-amount', function (int $cartId) {
    (new CartController())->getVatAmount($cartId);
});

route('GET', '/cart/([0-9]+)/get-total', function (int $cartId) {
    (new CartController())->getTotal($cartId);
});