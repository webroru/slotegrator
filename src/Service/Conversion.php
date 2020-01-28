<?php

declare(strict_types=1);

namespace App\Service;

class Conversion
{
    private const CONVERSION_RATE = 10;

    public function moneyToLoyalty(int $amount): int
    {
        return $amount * self::CONVERSION_RATE;
    }
}
