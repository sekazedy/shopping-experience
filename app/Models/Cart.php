<?php

declare(strict_types=1);

namespace App\Models;

use App\Exceptions\StockProductNotAvailableException;
use App\Exceptions\StockProductNotFoundException;
use App\Interfaces\CartInterface;
use App\Interfaces\MoneyInterface;
use App\Interfaces\ProductInterface;
use App\Traits\DatabaseTrait;
use PDO;

class Cart implements CartInterface
{
    use DatabaseTrait;

    private const TABLE = 'cart';

    private ?int $id = null;
    private float $subTotal;
    private float $vatAmount;

    public function __construct(float $subTotal = 0, float $vatAmount = 0, ?PDO $pdo = null)
    {
        $this->connect($pdo);
        $this->setTable(self::TABLE);

        $this->subTotal = $subTotal;
        $this->vatAmount = $vatAmount;
    }

	/**
	 * @param ProductInterface $product
	 * @return CartInterface
	 */
	public function addProduct(ProductInterface $product): self
    {
        $stockProduct = (new Stock($this->pdo))->getProductDataByName($product->getName());
        if (!$stockProduct) {
            throw new StockProductNotFoundException("Product with name '{$product->getName()}' was not found!");
        }

        if ($stockProduct['available'] === 0) {
            throw new StockProductNotAvailableException("Product '{$product->getName()}' stock is empty");
        }

        $productVatAmount = $stockProduct['price'] * $stockProduct['vat_rate'];

        if ($this->id) {
            $this->update([
                'subtotal' => $this->subTotal + $stockProduct['price'],
                'vat_amount' => $this->vatAmount + $productVatAmount,
            ], [['=', 'id', $this->id]]);
        } else {
            $this->id = $this->insert([
                'subtotal' => $stockProduct['price'],
                'vat_amount' => $productVatAmount,
            ]);
        }

        $this->addCartStockProduct($stockProduct['id']);

        $product->setAvailable($product->getAvailable() - 1);

        return $this;
	}

	/**
	 *
	 * @param ProductInterface $product
	 * @return CartInterface
	 */
	public function removeProduct(ProductInterface $product): self
    {
        $stockProduct = (new Stock($this->pdo))->getProductDataByName($product->getName());
        if (!$stockProduct) {
            throw new StockProductNotFoundException("Product with name '{$product->getName()}' was not found!");
        }

        $this->removeCartStockProduct($stockProduct['id']);

        $product->setAvailable($product->getAvailable() + 1);

        return $this;
	}

	/**
	 * @return array
	 */
	public function getProducts(): array
    {
        return [];
	}

	/**
	 * @return MoneyInterface
	 */
	public function getSubtotal(): MoneyInterface
    {
	}

	/**
	 * @return MoneyInterface
	 */
	public function getVatAmount(): MoneyInterface
    {
	}

	/**
	 * @return MoneyInterface
	 */
	public function getTotal(): MoneyInterface
    {
	}

    /**
	 * @return int|null
	 */
	public function getId(): ?int
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
            $cartStockProductStmt->execute();
        } else {
            $cartStockProductStmt = $this->pdo->prepare(
                'INSERT INTO cart_stock_products (cart_id, stock_product_id, quantity)
                VALUES (:cart_id, :stock_product_id, :quantity)'
            );

            $cartStockProductStmt->bindValue('cart_id', $this->id);
            $cartStockProductStmt->bindValue('stock_product_id', $stockProductId);
            $cartStockProductStmt->bindValue('quantity', 1);
            $cartStockProductStmt->execute();
        }
    }

    private function getCartStockProduct(int $stockProductId): array
    {
        $cartStockProductStmt = $this->pdo->prepare(
            'SELECT * FROM cart_stock_products WHERE cart_id = :cart_id AND stock_product_id = :product_id'
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
            $cartStockProductStmt->execute();
        } else {
            $cartStockProductStmt = $this->pdo->prepare(
                'UPDATE cart_stock_products SET quantity = :quantity
                WHERE cart_id = :cart_id AND stock_product_id = :stock_product_id'
            );

            $cartStockProductStmt->bindValue('quantity', $newQuantity);
            $cartStockProductStmt->bindValue('cart_id', $cartStockProduct['cart_id']);
            $cartStockProductStmt->bindValue('stock_product_id', $cartStockProduct['stock_product_id']);
            $cartStockProductStmt->execute();
        }
    }
}