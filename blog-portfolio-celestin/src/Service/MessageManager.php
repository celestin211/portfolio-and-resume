<?php

namespace App\Service;

use App\Entity\Message;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;

class MessageManager
{

    public function __construct(
        private readonly  EntityManagerInterface $em,
    )
    {

    }

    //Retourne l'ensemble des messages reçus et non lus par $user
    public function getMessagesNonLus(?Utilisateur $user)
    {
        return $this->em->getRepository(Message::class)->findAllMessagesByUser($user);
    }

    //Cette fonction vérifie si $utilisateur peut supprimer $message
    public function peutSupprimer(Utilisateur $utilisateur, $message)
    {
        //Si l'utilisateur n'est pas le destinataire du message à supprimer, on lève une exception denied access
        if (null === $message || $message->getDestinataire()->getId() != $utilisateur->getId()) {
            return false;
        }

        return true;
    }

    //Cette fonction vérifie si $utilisateur peut consulter $message
    public function peutConsulter(Utilisateur $utilisateur, $message)
    {
        return $this->peutSupprimer($utilisateur, $message);
    }

    //Cette fonction vérifie si $utilisateur peut restaurer $message
    public function peutRestaurer(Utilisateur $utilisateur, $message)
    {
        return $this->peutSupprimer($utilisateur, $message);
    }
}
