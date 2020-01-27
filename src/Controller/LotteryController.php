<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Award;
use App\Entity\Winning;
use App\Message\SendMoney;
use App\Service\Lottery;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
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
    public function transferMoney(Request $request, Lottery $lottery, EntityManagerInterface $em, MessageBusInterface $bus)
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
        if (!$accountNumber) {
            $errors[] = 'Account Number has not been specified';
        }

        if ($winning->getIsFinished()) {
            $errors[] = 'Award has already been sent';
        }

        if ($winning->getAward()->getType() !== Award::TYPE_MONEY) {
            $errors[] = 'Incorrect Award type';
        }

        if ($errors) {
            return $this->json([
                'errors' => $errors,
            ], 400);
        }

        $result = $lottery->sendMoney($winning, $accountNumber);
        if (!$result) {
            $bus->dispatch(new SendMoney($winning->getId(), $accountNumber));
        }

        return $this->json([
            'result' => $result,
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

        if ($errors) {
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
        $address = $request->request->get('address');
        /** @var Winning $winning */
        $winning = $em->find(Winning::class, $winningId);

        if (!$winning) {
            return $this->json([
                'errors' => ['Winning not found']
            ], 404);
        }

        $errors = [];
        if (!$address) {
            $errors[] = 'Address has not been specified';
        }

        if ($winning->getIsFinished()) {
            $errors[] = 'Award has already been sent';
        }

        if ($winning->getAward()->getType() !== Award::TYPE_PRIZE) {
            $errors[] = 'Incorrect Award type';
        }

        if ($errors) {
            return $this->json([
                'errors' => $errors,
            ], 400);
        }

        return $this->json([
            'result' => $lottery->sendPrize($winning, $address),
        ]);
    }

    /**
     * @Route("/convertMoney", name="api_convert_money", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function convertMoney(Request $request, Lottery $lottery, EntityManagerInterface $em)
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

        if ($winning->getAward()->getType() !== Award::TYPE_MONEY) {
            $errors[] = 'Incorrect Award type';
        }

        if ($errors) {
            return $this->json([
                'errors' => $errors,
            ], 400);
        }

        return $this->json([
            'result' => $lottery->convertMoney($winning),
        ]);
    }

    /**
     * @Route("/refusePrize", name="api_refuse_prize", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function refuse(Request $request, Lottery $lottery, EntityManagerInterface $em)
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

        if ($errors) {
            return $this->json([
                'errors' => $errors,
            ], 400);
        }

        return $this->json([
            'result' => $lottery->refuse($winning),
        ]);
    }
}
