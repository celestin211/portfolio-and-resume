<?php

namespace App\Service;


// src/Service/MailService.php

namespace App\Service;


use App\Entity\Utilisateur;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\TextPart;
use Symfony\Component\Mime\Part\Attachment;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;


use Twig\Environment;

class MailService
{

    //private $fromAddress;

    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly Environment $twig,
        private readonly UrlGeneratorInterface $router,
        // $fromAddress
    ) {
        //$this->fromAddress = $fromAddress;
    }

    /**
     * Envoie un email avec la facture en pièce jointe.
     */
    public function sendConfirmationEmail(Utilisateur $utilisateur, string $confirmationToken)
    {
        $url = $this->router->generate('confirmation_email', [
            'token' => $confirmationToken,
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        // Créer le contenu HTML de l'email avec Twig
        $htmlContent = $this->twig->render('emails/confirmation.html.twig', [
            'user' => $utilisateur,
            'confirmationUrl' => $url,
        ]);

        // Créer l'email
        $this->email = (new Email())
            ->from('ne-pas-repondre@jcplaton-couture.fr')
            ->to($utilisateur->getEmail())
            ->subject('Confirmez votre adresse e-mail')
            ->html($htmlContent);

        // Envoyer l'email
        $this->mailer->send($this->email);
    }

    public function sendMailRegister(Utilisateur $utilisateur)
    {

    }

}
