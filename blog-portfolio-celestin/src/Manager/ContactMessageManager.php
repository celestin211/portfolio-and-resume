<?php

namespace App\Manager;

use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;


class ContactMessageManager
{

    public function __construct
    (
        private readonly   EntityManagerInterface   $em,
    )
    {

    }

    public function listeContact()
    {

        $contacts = $this->em->getRepository(Contact::class)->findAll();

        $contactMessages = [];
        foreach ($contacts as $contact) {
            $contactMessages[] = [
                ucfirst($contact->getName()),
                ucwords($contact->getEmail()),
                mb_strtoupper($contact->getMessage()),
            ];
        }
        return $contactMessages;
    }
    public function create(Contact $contactMessage)
    {
        // On récupère le message de l'objet Contact
        $message = $contactMessage->getMessage();
    
        // On applique htmlspecialchars pour échapper les balises HTML dans le message
        $escapedMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    
        // On vérifie si l'agent existe déjà en base
        $contactBase = $this->em->getRepository(Contact::class)->findBy([
            'name' => $contactMessage->getName(),
            'email' => $contactMessage->getEmail(),
            'message' => $escapedMessage, // On passe le message échappé
        ]);
    
        if ($contactBase) {
            // Si l'entrée existe déjà, on retourne null
            return null;
        } else {
            // Si l'entrée n'existe pas, on persiste le nouvel objet Contact
            $this->em->persist($contactMessage);
            $this->em->flush();
    
            return $contactMessage; // Retourne l'objet Contact après insertion
        }
    }

    public function update(Contact $contactMessage)
    {
        // On vérifie si l'agent existe déjà en base
        $produitBase = $this->em->getRepository(Contact::class)->findOneBy([
            'name' => $contactMessage->getName(),
            'email' => $contactMessage->getEmail(),
            'message' => $contactMessage->getMessage(),
        ]);

        if ($produitBase && $produitBase->getId() != $contactMessage->getId()) {
            return null;
        } else {
            $this->em->persist($contactMessage);
            $this->em->flush();

            return $contactMessage;
        }
    }

    public function delete(Contact $contactMessage)
    {
        $this->em->remove($contactMessage);
        $this->em->flush();
    }

}
