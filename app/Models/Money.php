<?php

declare(strict_types=1);

namespace App\Models;

use App\Interfaces\MoneyInterface;

final class Money implements MoneyInterface
{
    private int $cents;
    private int $euros;

    public function __construct(int $cents = 0, int $euros = 0)
    {
        $this->cents = $cents;
        $this->euros = $euros;
    }

	/**
	 * @param int $cents
	 * @return Money
	 */
	public function setCents(int $cents): self
    {
        $this->cents = $cents;

        return $this;
	}

	/**
	 * @return int
	 */
	public function getCents(): int
    {
        return $this->cents;
	}

	/**
	 *
	 * @param int $euros
	 * @return Money
	 */
	public function setEuros(int $euros): self
    {
        $this->euros = $euros;

        return $this;
	}

	/**
	 * @return int
	 */
	public function getEuros(): int
    {
        return $this->euros;
	}

    /**
     * @return float
     */
    public function getFullPrice(): float
    {
        return (float)sprintf('%d.%02d', $this->euros, $this->cents);
    }
}