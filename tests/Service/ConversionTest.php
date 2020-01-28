<?php

namespace App\Tests\Service;

use App\Service\Conversion;
use PHPUnit\Framework\TestCase;

class ConversionTest extends TestCase
{
    public function testMoneyToLoyalty()
    {
        $moneyAmount = 1000;
        $conversion = new Conversion();
        $loyalty = $conversion->moneyToLoyalty($moneyAmount);

        $this->assertIsInt($loyalty);
        $this->assertGreaterThan($moneyAmount, $loyalty);
    }
}
