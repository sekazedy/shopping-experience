<?php

declare(strict_types=1);

namespace App\Models;

use App\Exceptions\DatabaseErrorException;
use App\Interfaces\ProductInterface;
use App\Interfaces\StockInterface;
use App\Traits\DatabaseTrait;
use PDO;
use PDOException;

final class Stock implements StockInterface
{
    use DatabaseTrait;

    private const TABLE = 'stock_products';

    private ?int $id = null;

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
        if ($this->duplicatesExist($product)) {
            throw new DatabaseErrorException('Error: duplicate product name found!');
        }

        $newId = $this->insert([
            'name' => $product->getName(),
            'available' => $product->getAvailable(),
            'price' => $product->getPrice()->getFullPrice(),
            'vat_rate' => $product->getVatRate(),
        ]);

        $this->id = $newId;

        return $this;
	}

	/**
	 *
	 * @param ProductInterface $product
	 * @return Stock
	 */
	public function removeProduct(ProductInterface $product): self
    {
        $this->delete([['=', 'name', $product->getName()]]);

        return $this;
	}

	/**
	 * @return array
	 */
	public function getProducts(): array
    {
        return $this->select();
	}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function duplicatesExist(ProductInterface $product): bool
    {
        return !empty($this->select([['=', 'name', $product->getName()]]));
    }

    public function getProductDataByName(string $name): array
    {
        return $this->getOne([['=', 'name', $name]]);
    }
}