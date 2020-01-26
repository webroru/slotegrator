<?php

declare(strict_types=1);

namespace App\Service;

class Payment
{
    public function send(string $accountNumber, int $amount): bool
    {
        // Payment Gate usage emulation.
        return true;
    }
}
