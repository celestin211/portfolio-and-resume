<?php


namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LogoutListener
{


    public function __construct(private  readonly  LoggerInterface $logger)
    {

    }

    public function onLogoutEvent(LogoutEvent $event)
    {
        $token = $event->getToken();

        // Récupérer l'utilisateur qui se déconnecte
        $user = $token->getUser();

        // Enregistrer un message dans les logs
        $this->logger->info('User logged out', [
            'username' => $user->getUsername(),
            'time' => date('Y-m-d H:i:s'),
        ]);

        $session = $event->getRequest()->getSession();
        $session->clear(); // Effacer toutes les données de la session
        $session->invalidate(); // Invalider la session
        session_destroy(); // Détruire la session PHP


        // Si vous voulez rediriger l'utilisateur après la déconnexion, vous pouvez également le faire :
        $response = new RedirectResponse('login');
        $event->setResponse($response);
    }
}
