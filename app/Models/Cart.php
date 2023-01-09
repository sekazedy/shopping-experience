<?php

declare(strict_types=1);

namespace App\Models;

use App\Exceptions\CartNotFoundException;
use App\Exceptions\StockProductNotAvailableException;
use App\Exceptions\StockProductNotFoundException;
use App\Exceptions\StockProductNotInCartException;
use App\Interfaces\CartInterface;
use App\Interfaces\MoneyInterface;
use App\Interfaces\ProductInterface;
use App\Traits\DatabaseTrait;
use PDO;

class Cart implements CartInterface
{
    use DatabaseTrait;

    private const TABLE = 'cart';

    private int $id;
    private float $subTotal;
    private float $vatAmount;

    public function __construct(int $id = 0, float $subTotal = 0, float $vatAmount = 0, ?PDO $pdo = null)
    {
        $this->connect($pdo);
        $this->setTable(self::TABLE);

        $this->id = $id;
        $this->subTotal = $subTotal;
        $this->vatAmount = $vatAmount;
    }

    public function create(): self
    {
        $cart = new self();
        $this->id = $cart->insert([
            'subtotal' => $this->subTotal,
            'vat_amount' => $this->vatAmount,
        ]);

        return $cart;
    }

	/**
	 * @param ProductInterface $product
	 * @return CartInterface
     * @throws StockProductNotFoundException
     * @throws StockProductNotAvailableException
     * @throws CartNotFoundException
	 */
	public function addProduct(ProductInterface $product): self
    {
        $stock = new Stock($this->pdo);

        $stockProduct = $stock->getProductDataByName($product->getName());
        if (!$stockProduct) {
            throw new StockProductNotFoundException("Product with name '{$product->getName()}' was not found!");
        }

        if ((int)$stockProduct['available'] === 0) {
            throw new StockProductNotAvailableException("Product '{$product->getName()}' stock is empty");
        }

        $price = $stockProduct['price'];
        $productVatAmount = bcmul($price, $stockProduct['vat_rate'], 2);

        if ($this->id) {
            $cartData = $this->getOne([['=', 'id', $this->id]]);
            if (!$cartData) {
                throw new CartNotFoundException("Cart with ID: {$this->id} was not found!");
            }

            $this->subTotal = floatval(bcadd(sprintf('%.2f', $cartData['subtotal']), $price, 2));
            $this->vatAmount = floatval(bcadd(sprintf('%.2f', $cartData['vat_amount']), $productVatAmount, 2));

            $this->update([
                'subtotal' => $this->subTotal,
                'vat_amount' => $this->vatAmount,
            ], [['=', 'id', $this->id]]);
        } else {
            $this->id = $this->insert([
                'subtotal' => $price,
                'vat_amount' => $productVatAmount,
            ]);

            $this->subTotal = floatval($price);
            $this->vatAmount = floatval($productVatAmount);
        }

        $this->addCartStockProduct($stockProduct['id']);

        $stock->updateAvailability($stockProduct, -1);

        $product->setAvailable((int)$stockProduct['available'] - 1);

        return $this;
	}

	/**
	 *
	 * @param ProductInterface $product
	 * @return CartInterface
     * @throws StockProductNotFoundException
     * @throws StockProductNotInCartException
     * @throws CartNotFoundException
	 */
	public function removeProduct(ProductInterface $product): self
    {
        $stock = new Stock($this->pdo);

        $stockProduct = $stock->getProductDataByName($product->getName());
        if (!$stockProduct) {
            throw new StockProductNotFoundException("Product with name '{$product->getName()}' was not found!");
        }

        if (!$this->getCartStockProduct($stockProduct['id'])) {
            throw new StockProductNotInCartException('Product was not found in cart!');
        }

        $productVatAmount = floatval(bcmul($stockProduct['price'], $stockProduct['vat_rate'], 2));
        $price = floatval($stockProduct['price']);

        $cartData = $this->getOne([['=', 'id', $this->id]]);
        if (!$cartData) {
            throw new CartNotFoundException("Cart with ID: {$this->id} was not found!");
        }

        $this->subTotal = floatval($cartData['subtotal']) - $price;
        $this->vatAmount = floatval($cartData['vat_amount']) - $productVatAmount;

        $this->update([
            'subtotal' => $this->subTotal,
            'vat_amount' => $this->vatAmount,
        ], [['=', 'id', $this->id]]);

        $this->removeCartStockProduct($stockProduct['id']);

        $stock->updateAvailability($stockProduct, 1);

        $product->setAvailable($product->getAvailable() + 1);

        return $this;
	}

	/**
	 * @return array
	 */
	public function getProducts(): array
    {
        $cartStockProductsStmt = $this->pdo->prepare(
            'SELECT stock_product_id FROM cart_stock_products WHERE cart_id = :cart_id'
        );

        $cartStockProductsStmt->bindValue('cart_id', $this->id);
        $cartStockProductsStmt->execute();

        $cartStockProductsIds = $cartStockProductsStmt->fetchAll(PDO::FETCH_COLUMN, 0);
        if (!$cartStockProductsIds) {
            return [];
        }

        $stock = new Stock($this->pdo);

        return $stock->getProductsByIds($cartStockProductsIds);
	}

	/**
	 * @return MoneyInterface
	 */
	public function getSubtotal(): MoneyInterface
    {
        $cartData = $this->getOne([['=', 'id', $this->id]], 'subtotal');
        if (!$cartData) {
            throw new CartNotFoundException("Cart with ID: {$this->id} was not found!");
        }

        $cents = bcsub($cartData['subtotal'], (string)((int)$cartData['subtotal']), 2) * 100;
        $euros = $cartData['subtotal'];

        return new Money((int)$cents, (int)$euros);
	}

	/**
	 * @return MoneyInterface
	 */
	public function getVatAmount(): MoneyInterface
    {
        $cartData = $this->getOne([['=', 'id', $this->id]], 'vat_amount');
        if (!$cartData) {
            throw new CartNotFoundException("Cart with ID: {$this->id} was not found!");
        }

        $cents = bcsub($cartData['vat_amount'], (string)((int)$cartData['vat_amount']), 2) * 100;
        $euros = $cartData['vat_amount'];

        return new Money((int)$cents, (int)$euros);
	}

	/**
	 * @return MoneyInterface
	 */
	public function getTotal(): MoneyInterface
    {
        $cartData = $this->getOne([['=', 'id', $this->id]]);
        if (!$cartData) {
            throw new CartNotFoundException("Cart with ID: {$this->id} was not found!");
        }

        $total = bcadd($cartData['subtotal'], $cartData['vat_amount'], 2);

        $cents = bcsub($total, (string)((int)$total), 2) * 100;
        $euros = $total;

        return new Money((int)$cents, (int)$euros);
	}

    public function getId(): int
    {
		return $this->id;
	}

    private function addCartStockProduct(int $stockProductId): void
    {
        $cartStockProduct = $this->getCartStockProduct($stockProductId);

        if ($cartStockProduct) {
            $cartStockProductStmt = $this->pdo->prepare(
                'UPDATE cart_stock_products SET quantity = :quantity
                WHERE cart_id = :cart_id AND stock_product_id = :stock_product_id'
            );

            $cartStockProductStmt->bindValue('quantity', $cartStockProduct['quantity'] + 1);
            $cartStockProductStmt->bindValue('cart_id', $cartStockProduct['cart_id']);
            $cartStockProductStmt->bindValue('stock_product_id', $cartStockProduct['stock_product_id']);
        } else {
            $cartStockProductStmt = $this->pdo->prepare(
                'INSERT INTO cart_stock_products (cart_id, stock_product_id, quantity)
                VALUES (:cart_id, :stock_product_id, :quantity)'
            );

            $cartStockProductStmt->bindValue('cart_id', $this->id);
            $cartStockProductStmt->bindValue('stock_product_id', $stockProductId);
            $cartStockProductStmt->bindValue('quantity', 1);
        }

        $cartStockProductStmt->execute();
    }

    private function getCartStockProduct(int $stockProductId): array
    {
        $cartStockProductStmt = $this->pdo->prepare(
            'SELECT * FROM cart_stock_products
            WHERE cart_id = :cart_id AND stock_product_id = :product_id LIMIT 1'
        );

        $cartStockProductStmt->bindValue('cart_id', $this->id);
        $cartStockProductStmt->bindValue('product_id', $stockProductId);
        $cartStockProductStmt->execute();

        return $cartStockProductStmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    private function removeCartStockProduct(int $stockProductId): void
    {
        $cartStockProduct = $this->getCartStockProduct($stockProductId);
        $newQuantity = $cartStockProduct['quantity'] - 1;

        if ($newQuantity < 1) {
            $cartStockProductStmt = $this->pdo->prepare(
                'DELETE FROM cart_stock_products WHERE cart_id = :cart_id AND stock_product_id = :product_id'
            );

            $cartStockProductStmt->bindValue('cart_id', $this->id);
            $cartStockProductStmt->bindValue('product_id', $stockProductId);
        } else {
            $cartStockProductStmt = $this->pdo->prepare(
                'UPDATE cart_stock_products SET quantity = :quantity
                WHERE cart_id = :cart_id AND stock_product_id = :stock_product_id'
            );

            $cartStockProductStmt->bindValue('quantity', $newQuantity);
            $cartStockProductStmt->bindValue('cart_id', $cartStockProduct['cart_id']);
            $cartStockProductStmt->bindValue('stock_product_id', $cartStockProduct['stock_product_id']);
        }

        $cartStockProductStmt->execute();
    }
}