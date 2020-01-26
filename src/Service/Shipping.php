<?php

declare(strict_types=1);

namespace App\Service;

class Shipping
{
    public function send(string $address, string $cargo): bool
    {
        // Game Gate usage emulation.
        return true;
    }
}
