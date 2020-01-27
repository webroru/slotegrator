<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Award;
use App\Entity\Winning;
use App\Service\Lottery;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LotteryController extends AbstractController
{
    /**
     * @Route("/play", name="api_play")
     * @IsGranted("ROLE_USER")
     */
    public function play(Lottery $lottery)
    {
        return $this->json(
            ['winning' => $lottery->getRandomAward()],
            200,
            [],
            ['groups' => ['api']]
        );
    }

    /**
     * @Route("/transferMoney", name="api_transfer_money", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function transferMoney(Request $request, Lottery $lottery, EntityManagerInterface $em)
    {
        $winningId = $request->request->get('winning_id');
        $accountNumber = $request->request->get('account_number');
        /** @var Winning $winning */
        $winning = $em->find(Winning::class, $winningId);

        if (!$winning) {
            return $this->json([
                'errors' => ['Winning not found']
            ], 404);
        }

        $errors = [];
        if ($winning->getIsFinished()) {
            $errors[] = 'Award has already been sent';
        }

        if ($winning->getAward()->getType() !== Award::TYPE_MONEY) {
            $errors[] = 'Incorrect Award type';
        }

        if (!$errors) {
            return $this->json([
                'errors' => $errors,
            ], 400);
        }

        return $this->json([
            'result' => $lottery->sendMoney($winning, $accountNumber),
        ]);
    }

    /**
     * @Route("/transferLoyalty", name="api_transfer_loyalty", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function transferLoyalty(Request $request, Lottery $lottery, EntityManagerInterface $em)
    {
        $winningId = $request->request->get('winning_id');
        /** @var Winning $winning */
        $winning = $em->find(Winning::class, $winningId);

        if (!$winning) {
            return $this->json([
                'errors' => ['Winning not found']
            ], 404);
        }

        $errors = [];
        if ($winning->getIsFinished()) {
            $errors[] = 'Award has already been sent';
        }

        if ($winning->getAward()->getType() !== Award::TYPE_LOYALTY) {
            $errors[] = 'Incorrect Award type';
        }

        if (!$errors) {
            return $this->json([
                'errors' => $errors,
            ], 400);
        }

        return $this->json([
            'result' => $lottery->sendLoyalty($winning),
        ]);
    }

    /**
     * @Route("/transferPrize", name="api_transfer_prize", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function transferPrize(Request $request, Lottery $lottery, EntityManagerInterface $em)
    {
        $winningId = $request->request->get('winning_id');
        /** @var Winning $winning */
        $winning = $em->find(Winning::class, $winningId);

        if (!$winning) {
            return $this->json([
                'errors' => ['Winning not found']
            ], 404);
        }

        $errors = [];
        if ($winning->getIsFinished()) {
            $errors[] = 'Award has already been sent';
        }

        if ($winning->getAward()->getType() !== Award::TYPE_PRIZE) {
            $errors[] = 'Incorrect Award type';
        }

        if (!$errors) {
            return $this->json([
                'errors' => $errors,
            ], 400);
        }

        return $this->json([
            'result' => $lottery->sendLoyalty($winning),
        ]);
    }
}