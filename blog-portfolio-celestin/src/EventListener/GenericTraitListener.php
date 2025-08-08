<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Interfaces\GenericTraitInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Security;

/**
 * Custom login listener.
 */
class GenericTraitListener
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    private function logAction(LifecycleEventArgs $args)
    {
        /* @var $entity GenericTraitInterface */
        $entity = $args->getObject();

        if (!$entity instanceof GenericTraitInterface) {
            return;
        }

        $utilisateur = $this->security->getUser();

        // Log de l'action de modification
        $entity->setModifiePar($utilisateur);
        $entity->setDateModification(new \DateTime('now'));

        // Log de l'action de création
        if (null === $entity->getId()) {
            $entity->setCreePar($utilisateur);
            $entity->setDateCreation(new \DateTime('now'));
        }
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $this->logAction($args);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->logAction($args);
    }
}
