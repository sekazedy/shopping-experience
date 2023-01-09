<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Models\Money;

/**
 * @covers Money
 */
final class MoneyTest extends BaseTestConfig
{
    public function test_setCents(): void
    {
        $cents = 12;
        $money = new Money();
        $money->setCents($cents);

        $this->assertEquals($cents, $money->getCents());
    }

    public function test_getCents(): void
    {
        $cents = 99;
        $money = new Money($cents);

        $this->assertEquals($cents, $money->getCents());
    }

    public function test_setEuros(): void
    {
        $euros = 3;
        $money = new Money();
        $money->setEuros($euros);

        $this->assertEquals($euros, $money->getEuros());
    }

    public function test_getEuros(): void
    {
        $euros = 105;
        $money = new Money(euros: $euros);

        $this->assertEquals($euros, $money->getEuros());
    }

    public function test_getFullPrice(): void
    {
        $money = new Money(4, 20);

        $this->assertEquals(20.04, (float)$money->getFormattedPrice());
    }
}