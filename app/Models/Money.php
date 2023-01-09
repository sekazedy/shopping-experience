<?php

declare(strict_types=1);

namespace App\Models;

use App\Interfaces\MoneyInterface;

/**
 * @method string getFormattedPrice()
 */
final class Money implements MoneyInterface
{
    private int $cents;
    private int $euros;

    public function __construct(int $cents = 0, int $euros = 0)
    {
        $eurosToAppend = 0;
        if ($cents > 99) {
            $eurosToAppend = (int)($cents / 100);
            $cents %= 100;
        }

        $this->cents = $cents;
        $this->euros = $euros + $eurosToAppend;
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
     * @return string
     */
    public function getFormattedPrice(): string
    {
        return sprintf('%d.%02d', $this->euros, $this->cents);
    }
}