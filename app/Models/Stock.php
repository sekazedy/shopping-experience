<?php

declare(strict_types=1);

namespace App\Models;

use App\Interfaces\ProductInterface;
use App\Interfaces\StockInterface;
use App\Traits\DatabaseTrait;
use PDO;

final class Stock implements StockInterface
{
    use DatabaseTrait;

    private const TABLE = 'stock';

    private int $id;
    private int $productId;
    private int $quantity = 0;

    public function __construct(?PDO $pdo = null)
    {
        $this->connect($pdo);
        $this->setTable(self::TABLE);
    }

	/**
	 * @param ProductInterface $product
	 * @return Stock
	 */
	public function addProduct(ProductInterface $product): self
    {
        $record = $this->getOne([['=', 'product_id', $product->getId()]]);
        $quantity = 1;

        if ($record) {
            $quantity = $record['quantity'] + 1;

            $this->update(
                ['quantity' => $quantity],
                [['=', 'id', $record['id']]]
            );

            $this->id = $record['id'];
            $this->productId = $record['product_id'];
            $this->quantity = $quantity;
        } else {
            $newId = $this->insert([
                'product_id' => $product->getId(),
                'quantity' => $quantity,
            ]);

            $this->id = $newId;
            $this->productId = $product->getId();
            $this->quantity = $quantity;
        }

        return $this;
	}

	/**
	 *
	 * @param ProductInterface $product
	 * @return Stock
	 */
	public function removeProduct(ProductInterface $product): self
    {
        if ($this->quantity > 0) {
            --$this->quantity;

            $this->update(['quantity' => $this->quantity], [['=', 'product_id', $product->getId()]]);
        }

        return $this;
	}

	/**
	 * @return array
	 */
	public function getProducts(): array
    {
        return $this->select();
	}

	/**
	 * @return int
	 */
	public function getQuantity(): int
    {
		return $this->quantity;
	}
}