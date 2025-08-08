<?php

// src/EventListener/StockUpdatedListener.php

namespace App\EventListener;

use App\EventListener\StockUpdatedEvent;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class StockUpdatedListener
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function onStockUpdated(StockUpdatedEvent $event): void
    {
        $produit = $event->getProduit();

        // Envoi d'un e-mail d'alerte
        $email = (new Email())
            ->from('admin@ecommerce.com')
            ->to('gestion-stock@ecommerce.com')
            ->subject('Alerte de réapprovisionnement')
            ->text("Réapprovisionnement nécessaire pour le produit : " . $produit->getNom());

        $this->mailer->send($email);
    }
}
