<?php

declare(strict_types=1);

namespace App\Models;

use App\Interfaces\MoneyInterface;
use App\Interfaces\ProductInterface;

final class Product implements ProductInterface
{
    private string $name;
    private int $available;
    private MoneyInterface $price;
    private float $vat_rate;

    public function __construct(
        string $name = '',
        int $available = 0,
        MoneyInterface $price = null,
        float $vatRate = 0
    ) {
        $this->name = $name;
        $this->available = $available;
        $this->price = $price ?? new Money();
        $this->vat_rate = $vatRate;
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
	 * @param MoneyInterface $price
	 * @return ProductInterface
	 */
	public function setPrice(MoneyInterface $price): self
    {
        $this->price = $price;

        return $this;
	}

	/**
	 * @return MoneyInterface
	 */
	public function getPrice(): MoneyInterface
    {
        return $this->price;
	}

	/**
	 *
	 * @param float $vatRate
	 * @return ProductInterface
	 */
	public function setVatRate(float $vatRate): self
    {
        $this->vat_rate = $vatRate;

        return $this;
	}

	/**
	 * @return float
	 */
	public function getVatRate(): float
    {
        return $this->vat_rate;
	}
}