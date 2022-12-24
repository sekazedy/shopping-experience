<?php

declare(strict_types=1);

namespace App\Models;

use App\Interfaces\MoneyInterface;
use App\Interfaces\ProductInterface;

final class Product implements ProductInterface
{
    public const NOT_AVAILABLE = 0;
    public const AVAILABLE = 1;

    private string $name;
    private int $available;
    private MoneyInterface $price;
    private float $vat;

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
}