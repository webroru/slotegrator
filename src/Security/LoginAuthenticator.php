<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class LoginAuthenticator extends AbstractGuardAuthenticator
{
    private $passwordEncoder;
    private $em;
    private $security;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $em,
        Security $security
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->em = $em;
        $this->security = $security;
    }

    public function supports(Request $request): bool
    {
        if ($this->security->getUser()) {
            return false;
        }

        $isLoginRequest = $request->get('_route') === 'api_login' && $request->isMethod('POST');
        return $isLoginRequest || $request->headers->has('X-AUTH-TOKEN');
    }

    public function getCredentials(Request $request): array
    {
        return [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'token' => $request->headers->get('X-AUTH-TOKEN'),
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        if ($credentials['token']) {
            return $this->em->getRepository(User::class)
                ->findOneBy(['apiToken' => $credentials['token']]);
        } elseif ($credentials['email']) {
            return $userProvider->loadUserByUsername($credentials['email']);
        } else {
            return null;
        }
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        if ($credentials['password']) {
            return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
        } else {
            return true;
        }
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        return new JsonResponse([
            'error' => $exception->getMessageKey()
        ], 400);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    public function start(Request $request, AuthenticationException $authException = null): JsonResponse
    {
        return new JsonResponse([
            'error' => 'Access Denied'
        ]);
    }

    public function supportsRememberMe(): bool
    {
        return false;
    }
}
