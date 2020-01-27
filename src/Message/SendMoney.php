<?php

declare(strict_types=1);

namespace App\Message;

class SendMoney
{
    private $winningId;
    private $accountNumber;

    public function __construct(int $winningId, string $accountNumber)
    {
        $this->winningId = $winningId;
        $this->accountNumber = $accountNumber;
    }

    public function getWinningId(): int
    {
        return $this->winningId;
    }

    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }
}
