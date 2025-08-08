<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class SecurityVoter extends Voter
{
    protected EntityManagerInterface $em;
    private Security $security;
    private bool $isSsoEnabled;

    // Liste des actions supportées
    final public const CHANGER_MOT_DE_PASSE = 'api_changer_mot_de_passe';

    public function __construct(EntityManagerInterface $em, Security $security, bool $isSsoEnabled)
    {
        $this->em = $em;
        $this->security = $security;
        $this->isSsoEnabled = $isSsoEnabled;
    }

    protected function supports($attribute, $subject): bool
    {
        // Si l'attribut n'est pas supporté, return false
        if (!in_array($attribute, [
            self::CHANGER_MOT_DE_PASSE,
        ])) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $utilisateurConnecte = $this->security->getUser();

        if (!$utilisateurConnecte instanceof Utilisateur) {
            // Si l'utilisateur n'est pas connecté, l'accès est refusé
            return false;
        }

        switch ($attribute) {
            case self::CHANGER_MOT_DE_PASSE:
                return $this->peutChangerMotDePasse();
        }

        throw new \LogicException("Erreur de logique dans SecurityVoter : type d'accès $attribute non géré !");
    }

    private function peutChangerMotDePasse()
    {
        if (!$this->isSsoEnabled) {
            return true;
        }

        // Dans tous les autres cas, on refuse l'accès
        return false;
    }
}
