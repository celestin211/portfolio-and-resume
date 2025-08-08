<?php

namespace App\Security;

use App\Repository\UtilisateurRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ChangePasswordAuthenticator extends AbstractAuthenticator
{


    public function __construct(private readonly  UserProviderInterface $userProvider, private  readonly UtilisateurRepository $utilisateurRepository)
    {
    }

    public function authenticate(Request $request): Passport
    {
        $confirmationToken = $request->query->get('confirmationToken', '');
        $userIdentifier = $this->utilisateurRepository->findOneBy(['confirmationToken' => $confirmationToken]);

        $userBadge = new UserBadge($userIdentifier, function ($userIdentifier) {
            return $this->userProvider->loadUserByIdentifier($userIdentifier);
        });

        return new SelfValidatingPassport($userBadge);
    }

    public function supports(Request $request): ?bool
    {
        return $request->isMethod('POST') && str_starts_with($request->getPathInfo(), '/reinitialisation-mot-de-passe');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return null;
    }
}
