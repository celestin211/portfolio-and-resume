<?php

namespace App\Security;

use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class TokenAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UtilisateurRepository $utilisateurRepository,
        private readonly JWTTokenManagerInterface $jwtManager,
        private readonly string $jwtSecretKey
    ) {}

    public function supports(Request $request): bool
    {
        return $request->headers->has('x-auth-token');
    }

    public function authenticate(Request $request): SelfValidatingPassport
    {

        // Vérifier si le token JWT est présent dans les en-têtes
        $authorizationHeader = $request->headers->get('Authorization');
        if (!$authorizationHeader) {
            throw new CustomUserMessageAuthenticationException('JWT token not found');
        }

        // Récupérer le jeton JWT
        $jwt = str_replace('Bearer ', '', $authorizationHeader);

        // Vérifier si le token est valide
        try {
            $user = $this->jwtManager->parse($jwt);
        } catch (\Exception $e) {
            throw new CustomUserMessageAuthenticationException('Invalid JWT token');
        }

        // Si le jeton est valide, retourner un Passport avec l'utilisateur associé
        return new SelfValidatingPassport(
            new UserBadge($user->getEmail(), function ($email) {
                return $this->utilisateurRepository->findOneBy(['email' => $email]);
            })
        );
    }


    public function onAuthenticationSuccess(Request $request, $token, string $firewallName): ?Response
    {
        return null; // allow request to continue
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        return new JsonResponse([
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new JsonResponse([
            'message' => 'Authentication Required',
        ], Response::HTTP_UNAUTHORIZED);
    }

}
