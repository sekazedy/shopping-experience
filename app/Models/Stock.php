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

    public function __construct(?PDO $pdo = null)
    {
        $this->connect($pdo);
    }

	/**
	 * @param ProductInterface $product
	 * @return Stock
	 */
	public function addProduct(ProductInterface $product): self
    {
        $this->insert(self::TABLE, $product->toArray());

        return $this;
	}

	/**
	 *
	 * @param ProductInterface $product
	 * @return Stock
	 */
	public function removeProduct(ProductInterface $product): self
    {
        return $this;
	}

	/**
	 * @return array
	 */
	public function getProducts(): array
    {
        return [];
	}
}