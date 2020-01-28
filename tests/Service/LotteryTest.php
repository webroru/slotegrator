<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Entity\Winning;
use App\Service\Game;
use App\Service\Lottery;
use App\Service\Payment;
use App\Service\Shipping;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Security;

class LotteryTest extends TestCase
{
    public function testConvertMoney()
    {
        $user = $this->createMock(User::class);
        $entityManager = $this->createMock(EntityManager::class);
        $security = $this->createMock(Security::class);
        $payment = $this->createMock(Payment::class);
        $game = $this->createMock(Game::class);
        $shipping = $this->createMock(Shipping::class);

        $user->expects($this->any())
            ->method('getId')
            ->willReturn(0);

        $game->expects($this->any())
            ->method('send')
            ->willReturn(true);

        $entityManager->expects($this->any())
            ->method('flush');

        $winning = (new Winning())
            ->setAmount(0)
            ->setUser($user);

        $lottery = new Lottery($entityManager, $security, $payment, $game, $shipping);
        $this->assertTrue($lottery->convertMoney($winning));
    }
}
