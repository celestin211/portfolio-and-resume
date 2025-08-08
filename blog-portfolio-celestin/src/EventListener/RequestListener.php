<?php

namespace App\EventListener;

use App\Entity\Connexion;
use App\Entity\User;
use App\Manager\SecurityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\LockedException;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class RequestListener implements EventSubscriberInterface
{


    private $nbMaxConnexionKo;

    private $maxIdleTime;

    /** @var bool */
    private $isSsoEnabled;

    public function __construct(
        private readonly TokenStorageInterface $securityContext,
        private readonly RouterInterface $router,
        private readonly EntityManagerInterface $em,
        private readonly SecurityManager $securityManager,
        private readonly RequestStack $requestStack,
        private readonly AuthorizationCheckerInterface $checker,
        $nbMaxConnexionKo,
        $maxIdleTime,
        bool $isSsoEnabled
    ) {
        $this->nbMaxConnexionKo = $nbMaxConnexionKo;
        $this->maxIdleTime = $maxIdleTime;
        $this->isSsoEnabled = $isSsoEnabled;
    }

    public function onChangePassword(RequestEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $path = $this->router->generate('api_utilisateur_change_password');

        if ('_wdt' === $event->getRequest()->get('_route')) {
            return;
        }

        if (!$this->securityContext->getToken()) {
            return;
        }

        $utilisateur = $this->securityContext->getToken()->getUser();

        if (!$utilisateur instanceof User) {
            return;
        }

        if ($event->getRequest()->getPathInfo() === $path) {
            return;
        }

        if ($utilisateur->isForceChangePassword()) {
            $event->setResponse(new RedirectResponse($path));
        }

        return;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        /** @var $user User */
        $user = $event->getAuthenticationToken()->getUser();

        if (!$this->isSsoEnabled) {
            $user->setNbConnexionKO(0);
        }

        $connexion = new Connexion($user);

        $this->em->persist($connexion);
        $this->em->flush();
    }

    public function onSecurityAuthenticationFailure(LoginFailureEvent $event, string $confirmToken)
    {
        if ($this->isSsoEnabled) {
            return;
        }

        $email = $event->getRequest()->get('email');

        if (empty($email)) {
            return;
        }

        /** @var $utilisateur User */
        $utilisateur = $this->em->getRepository(User::class)->findOneBy(['email' => $email, 'enabled' => true]);

        if (!$utilisateur) {
            return;
        }

        $utilisateur->setNbConnexionKO($utilisateur->getNbConnexionKO() + 1);

        if ($utilisateur->getNbConnexionKO() > $this->nbMaxConnexionKo) {
            $utilisateur->setLocked(true);

            $this->securityManager->demandeReinitialisationMotDePasse($email, $confirmToken);
            $this->em->flush();
            throw new LockedException('Compte bloqué : veuillez consulter votre messagerie pour débloquer votre compte utilisateur');
        }

        $this->em->flush();
        throw new CustomUserMessageAuthenticationException('Email ou mot de passe incorrect');
    }

    public function onSessionTimeout(RequestEvent $event)
    {
        if (!$event->isMainRequest()
            || $this->maxIdleTime <= 0
            || $this->isAuthenticatedAnonymously()) {
            return;
        }

        $session = $this->requestStack->getSession();
        $session->start();

        if ((time() - $session->getMetadataBag()->getLastUsed()) <= $this->maxIdleTime) {
            return;
        }

        $this->securityContext->setToken();
        $session->getFlashBag()->set('notice', 'Vous avez été déconnecté pour cause d\'inactivité.');

        $event->setResponse(new RedirectResponse($this->router->generate('app_login')));
    }

    private function isAuthenticatedAnonymously(): bool
    {
        return !$this->securityContext->getToken()
            || !$this->checker->isGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onChangePassword'], ['onSessionTimeout']],
            SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin',
            'Symfony\Component\Security\Http\Event\LoginFailureEvent' => 'onSecurityAuthenticationFailure'
        ];
    }
}
