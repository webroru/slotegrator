<?php

declare(strict_types=1);

namespace App\Service;

class Game
{
    public function send(int $userId, int $amount): bool
    {
        // Game Gate usage emulation.
        return true;
    }
}
