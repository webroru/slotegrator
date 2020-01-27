<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\Award;
use App\Entity\Winning;
use App\Message\SendMoney;
use App\Service\Lottery;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class SendMoneyHandler implements MessageHandlerInterface
{
    private $em;
    private $lottery;
    private $bus;

    public function __construct(EntityManagerInterface $em, Lottery $lottery, MessageBusInterface $bus)
    {
        $this->em = $em;
        $this->lottery = $lottery;
        $this->bus = $bus;
    }

    public function __invoke(SendMoney $sendMoney)
    {
        /** @var Winning $winning */
        $winning = $this->em->find(Winning::class, $sendMoney->getWinningId());
        $accountNumber = $sendMoney->getAccountNumber();
        if (
            !$accountNumber ||
            !$winning ||
            $winning->getIsFinished() ||
            $winning->getAward()->getType() !== Award::TYPE_MONEY
        ) {
            return;
        }
        $result = $this->lottery->sendMoney($winning, $sendMoney->getAccountNumber());
        if (!$result) {
            $this->bus->dispatch(new SendMoney($winning->getId(), $accountNumber));
        }
    }
}
