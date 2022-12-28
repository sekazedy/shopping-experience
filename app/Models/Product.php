<?php

declare(strict_types=1);

namespace App\Models;

use App\Interfaces\MoneyInterface;
use App\Interfaces\ProductInterface;
use App\Traits\DatabaseTrait;
use PDO;

final class Product implements ProductInterface
{
    use DatabaseTrait;

    public const NOT_AVAILABLE = 0;
    public const AVAILABLE = 1;

    private ?int $id = null;

    private string $name;
    private int $available;
    private MoneyInterface $price;
    private float $vat;

    private const TABLE = 'products';

    public function __construct(
        string $name = '',
        int $available = self::NOT_AVAILABLE,
        MoneyInterface $price = null,
        float $vat = 0,
        ?PDO $pdo = null
    ) {
        $this->connect($pdo);
        $this->setTable(self::TABLE);

        $this->name = $name;
        $this->available = $available;
        $this->price = $price ?? new Money();
        $this->vat = $vat;
    }

    /**
	 * @param int $id
	 * @return self
	 */
	public function setId(int $id): self
    {
		$this->id = $id;
		return $this;
	}

    /**
	 * @return int
	 */
	public function getId(): ?int
    {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getName(): string
    {
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return self
	 */
	public function setName(string $name): self
    {
		$this->name = $name;

		return $this;
	}

	/**
	 * @param int $available
	 * @return ProductInterface
	 */
	public function setAvailable(int $available): self
    {
        $this->available = $available;

        return $this;
	}

	/**
	 * @return int
	 */
	public function getAvailable(): int
    {
        return $this->available;
	}

	/**
	 *
	 * @param \App\Interfaces\MoneyInterface $price
	 * @return ProductInterface
	 */
	public function setPrice(MoneyInterface $price): self
    {
        $this->price = $price;

        return $this;
	}

	/**
	 * @return \App\Interfaces\MoneyInterface
	 */
	public function getPrice(): MoneyInterface
    {
        return $this->price;
	}

	/**
	 *
	 * @param float $vat
	 * @return ProductInterface
	 */
	public function setVatRate(float $vat): self
    {
        $this->vat = $vat;

        return $this;
	}

	/**
	 * @return float
	 */
	public function getVatRate(): float
    {
        return $this->vat;
	}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'available' => $this->available,
            'price' => $this->price->getFullPrice(),
            'vat' => $this->vat,
        ];
    }

    public function create(): self
    {
        $this->setId(
            $this->insert($this->toArray())
        );

        return $this;
    }
}