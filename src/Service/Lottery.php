<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Award;
use App\Entity\Winning;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class Lottery
{
    private const MONEY_INTERVAL = [10, 1000];
    private const LOYALTY_INTERVAL = [10, 1000];

    private $em;
    private $security;
    private $payment;
    private $game;
    private $shipping;

    public function __construct(
        EntityManagerInterface $em,
        Security $security,
        Payment $payment,
        Game $game,
        Shipping $shipping
    ) {
        $this->em = $em;
        $this->security = $security;
        $this->payment = $payment;
        $this->game = $game;
        $this->shipping = $shipping;
    }

    public function getRandomAward(): Winning
    {
        $type = Award::TYPES[array_rand(Award::TYPES)];
        switch ($type) {
            case Award::TYPE_MONEY:
                $wining = $this->getMoney();
                break;
            case Award::TYPE_LOYALTY:
                $wining = $this->getLoyalty();
                break;
            case Award::TYPE_PRIZE:
                $wining = $this->getPrize();
                break;
            default:
                throw new \Exception("Undefined Award Type: $type");
        }
        $this->em->persist($wining);
        $this->em->flush();
        return $wining;
    }

    public function sendMoney(Winning $winning, string $accountNumber): bool
    {
        $amount = $winning->getAmount();
        $reserve = $winning->getAward()->getAmount();
        if ($reserve < $amount) {
            return false;
        }
        $result = $this->payment->send($accountNumber, $amount);
        if ($result === true) {
            $winning->setFinished()
                ->getAward()->setAmount($reserve - $amount);
            $this->em->flush();
        }
        return $result;
    }

    public function sendLoyalty(Winning $winning): bool
    {
        $amount = $winning->getAmount();
        $result = $this->game->send($this->security->getUser()->getId(), $amount);
        if ($result === true) {
            $winning->setFinished();
            $this->em->flush();
        }
        return $result;
    }

    public function sendPrize(Winning $winning, string $address): bool
    {
        $reserve = $winning->getAward()->getAmount();
        if (--$reserve < 0) {
            return false;
        }
        $result = $this->shipping->send($address, $winning->getAward()->getName());
        if ($result === true) {
            $winning->setFinished()
            ->getAward()->setAmount($reserve);
            $this->em->flush();
        }
        return $result;
    }

    private function getMoney(): Winning
    {
        $money = $this->em->getRepository(Award::class)->findOneBy(['type' => Award::TYPE_MONEY]);
        $amount = rand(self::MONEY_INTERVAL[0], self::MONEY_INTERVAL[1]);
        return (new Winning())
            ->setUser($this->security->getUser())
            ->setAward($money)
            ->setAmount($amount)
            ->setUnFinished();
    }

    private function getLoyalty()
    {
        $loyalty = $this->em->getRepository(Award::class)->findOneBy(['type' => Award::TYPE_LOYALTY]);
        $amount = rand(self::LOYALTY_INTERVAL[0], self::LOYALTY_INTERVAL[1]);
        return (new Winning())
            ->setUser($this->security->getUser())
            ->setAward($loyalty)
            ->setAmount($amount)
            ->setUnFinished();
    }

    private function getPrize()
    {
        $prizes = $this->em->getRepository(Award::class)->getAvailablePrizes();
        if (!$prizes) {
            return $this->getRandomAward();
        }

        return (new Winning())
            ->setUser($this->security->getUser())
            ->setAward($prizes[array_rand($prizes)])
            ->setAmount(1)
            ->setUnFinished();
    }
}
