<?php

declare(strict_types=1);

namespace App\Security\AdminSecurity;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class AdminSecurityVoter extends Voter
{
    protected EntityManagerInterface $em;
    private Security $security;
    private bool $isSsoEnabled;

    // Liste des actions supportées
   
    final public const CHANGE_INIT_PASSWORD = 'reinitialisation_mot_de_passe';
    final public const CONSULTER_PROFILE = 'consulter_profile';
    final public const CREER_UTILISATEUR = 'creer_utilisateur';

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
            self::CHANGER_MOT_DE_PASSEWORD,
            self::CHANGE_INIT_PASSWORD,
            self::CONSULTER_PROFILE,
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
            case self::CHANGER_MOT_DE_PASSEWORD:
                return $this->peutChangerMotDePasse();
            case self::CHANGE_INIT_PASSWORD:
                return $this->peutChangerInitialiserMotDePasse();
            case self::CONSULTER_PROFILE:
                return $this->peutConsulter();
            case self::CREER_UTILISATEUR:
                return $this->peutCreer();
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

    private function peutChangerInitialiserMotDePasse()
    {
        if (!$this->isSsoEnabled) {
            return true;
        }
        // Dans tous les autres cas, on refuse l'accès
        return false;
    }

    private function peutConsulter(Utilisateur $utilisateur): bool
    {
        if ($utilisateur->hasRole('ROLE_ADMIN_VIP') || ('ROLE_ADMIN')) {
            return false;
        }

        return true;
    }
    private function peutCreer(): bool
    {
        if ($utilisateur->hasRole('ROLE_ADMIN_VIP') || ('ROLE_ADMIN')) {
            return false;
        }

        return true;
    }
}
