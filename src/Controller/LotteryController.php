<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Entity\Winning;
use App\Service\Lottery;
use App\Service\Payment;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

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

        if ($winning->getIsFinished()) {
            return $this->json([
                'errors' => ['Award has already been sent']
            ], 400);
        }

        return $this->json([
            'result' => $lottery->sendMoney($winning, $accountNumber),
        ]);
    }

    /**
     * @Route("/transferLoyalty", name="api_transfer_loyalty", methods={"POST"})
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

        if ($winning->getIsFinished()) {
            return $this->json([
                'errors' => ['Award has already been sent']
            ], 400);
        }

        return $this->json([
            'result' => $lottery->sendLoyalty($winning),
        ]);
    }

    /**
     * @Route("/transferPrize", name="api_transfer_prize", methods={"POST"})
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

        if ($winning->getIsFinished()) {
            return $this->json([
                'errors' => ['Award has already been sent']
            ], 400);
        }

        return $this->json([
            'result' => $lottery->sendLoyalty($winning),
        ]);
    }


//    /**
//     * @Route("/register", name="api_register", methods={"POST"})
//     */
//    public function register(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder, Request $request)
//    {
//        $email = $request->request->get('email');
//        $password = $request->request->get('password');
//        $passwordConfirmation = $request->request->get('password_confirmation');
//
//        $errors = [];
//        if ($password !== $passwordConfirmation) {
//            $errors[] = 'Password does not match the password confirmation.';
//        }
//
//        if ($errors) {
//            return $this->json([
//                'errors' => $errors
//            ], 400);
//        }
//
//        $user = new User();
//        $encodedPassword = $passwordEncoder->encodePassword($user, $password);
//        $user->setEmail($email)
//            ->setPassword($encodedPassword);
//
//        try {
//            $em->persist($user);
//            $em->flush();
//
//            return $this->json(
//                ['user' => $user],
//                200,
//                [],
//                ['groups' => ['api']]
//            );
//        } catch (UniqueConstraintViolationException $e) {
//            $errors[] = 'The email provided already has an account!';
//        } catch (\Exception $e) {
//            $errors[] = 'Unable to save new user at this time.';
//        }
//
//        return $this->json([
//            'errors' => $errors
//        ], 400);
//    }
//
//    /**
//     * @Route("/login", name="api_login", methods={"POST"})
//     */
//    public function login()
//    {
//        return $this->json(['result' => true]);
//    }
}
