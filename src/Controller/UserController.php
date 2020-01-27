<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/register", name="api_register", methods={"POST"})
     */
    public function register(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder, Request $request)
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $passwordConfirmation = $request->request->get('password_confirmation');

        $errors = [];
        if ($password !== $passwordConfirmation) {
            $errors[] = 'Password does not match the password confirmation.';
        }

        if ($errors) {
            return $this->json([
                'errors' => $errors
            ], 400);
        }

        $user = new User();
        $encodedPassword = $passwordEncoder->encodePassword($user, $password);
        $user->setEmail($email)
            ->setPassword($encodedPassword);

        try {
            $em->persist($user);
            $em->flush();

            return $this->json(
                ['user' => $user],
                200,
                [],
                ['groups' => ['api']]
            );
        } catch (UniqueConstraintViolationException $e) {
            $errors[] = 'The email provided already has an account!';
        } catch (\Exception $e) {
            $errors[] = 'Unable to save new user at this time.';
        }

        return $this->json([
            'errors' => $errors
        ], 400);
    }

    /**
     * @Route("/login", name="api_login", methods={"POST"})
     */
    public function login()
    {
        return $this->json(['result' => true]);
    }
}
