<?php

declare(strict_types=1);

namespace App\Security\AdminSecurity;

use App\Entity\Utilisateur;
use App\EnumTypes\EnumRole;
use App\Service\RoleService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class AdminUtilisateursVoter extends Voter
{

    // Liste des actions supportées
    final public const LISTER = 'admin_utilisateurS';
    final public const MODIFIER = 'admin_utilisateur_modifier';
    final public const SUPPRIMER = 'admin_utilisateur_supprimer';



    public function __construct(
        protected readonly EntityManagerInterface $em,
        private  readonly Security $security,
        private readonly RoleService $roleService
    )
    {
    }

    protected function supports($attribute, $subject): bool
    {
        // Si l'attribut n'est pas supporté, return false
        if (!in_array($attribute, [
            self::MODIFIER,
            self::LISTER,
            self::SUPPRIMER,
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
            case self::LISTER:
                return $this->peutLister($utilisateurConnecte);
            case self::MODIFIER:
                return $this->peutModifier($utilisateurConnecte);

                case self::SUPPRIMER:
                return $this->peutSupprimer($utilisateurConnecte);
        }

        throw new \LogicException("Erreur de logique dans SecurityVoter : type d'accès $attribute non géré !");
    }

    private function peutLister(Utilisateur $utilisateur): bool
    {
        if ($this->roleService->isGranted('ROLE_ADMIN', $utilisateur) || $this->roleService->isGranted('ROLE_ADMIN_VIP', $utilisateur)) {
            return true;
        }

        if ($this->roleService->isGranted('ROLE_ADMIN_VIP', $utilisateur)) {
            return true;
        }

        // Dans tous les autres cas, on refuse l'accès
        return false;
    }
    private function peutSupprimer(Utilisateur $utilisateur): bool
    {
        if ($this->roleService->isGranted('ROLE_ADMIN', $utilisateur) || $this->roleService->isGranted('ROLE_ADMIN_VIP', $utilisateur)) {
            return true;
        }

        if ($this->roleService->isGranted('ROLE_ADMIN_VIP', $utilisateur)) {
            return true;
        }

        // Dans tous les autres cas, on refuse l'accès
        return false;
    }

    private function peutModifier(Utilisateur $utilisateur): bool
    {
        if ($this->roleService->isGranted('ROLE_ADMIN', $utilisateur) || $this->roleService->isGranted('ROLE_ADMIN_VIP', $utilisateur)) {
            return true;
        }

        if ($this->roleService->isGranted('ROLE_ADMIN_VIP', $utilisateur)) {
            return true;
        }

        // Dans tous les autres cas, on refuse l'accès
        return false;
    }
}
