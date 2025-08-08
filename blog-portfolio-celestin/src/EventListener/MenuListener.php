<?php

namespace App\EventListener;

use App\Entity\Utilisateur;
use App\Util\Menu;
use App\Repository\MessageRepository;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Security;

class MenuListener
{
    private Security $securityContext;
    private AuthorizationCheckerInterface $securityAuthorizationChecker;
    private RouterInterface $router;
    private MessageRepository $messageRepository;

    public function __construct(Security $securityContext, AuthorizationCheckerInterface $securityAuthorizationChecker, RouterInterface $router, MessageRepository $messageRepository)
    {
        $this->router = $router;
        $this->securityAuthorizationChecker = $securityAuthorizationChecker;
        $this->securityContext = $securityContext;
        $this->messageRepository = $messageRepository;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if (null !== $this->securityContext->getToken()) {
            $user = $this->securityContext->getToken()->getUser();

            if (null !== $user && $user instanceof Utilisateur) {
                $nbMessagesNonLus = $this->messageRepository->countUnreadMessages($user);
                $messagesNonLus = $this->messageRepository->findUnreadMessages($user);

                $menu = Menu::getMenu($user, $this->securityAuthorizationChecker, $this->router, $nbMessagesNonLus, $messagesNonLus);

                $menu = Menu::setActiveMenu($event->getRequest()->attributes->get('_route'), $menu);

                $event->getRequest()->getSession()->set('menu', $menu);

                $breadCrumb = Menu::getPathMenu($menu);

                $event->getRequest()->getSession()->set('breadCrumb', $breadCrumb);
            }
        }
    }
}
