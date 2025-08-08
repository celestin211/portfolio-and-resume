<?php

declare(strict_types=1);

namespace App\Security\AdminSecurity;

use App\Entity\Produit;
use App\Entity\Utilisateur;
use App\EnumTypes\EnumRole;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class AdminProduitVoter extends Voter
{
    private Security $security;
    private AuthorizationCheckerInterface $roleService;

    // Liste des actions supportées
    final public const SUPPRIMER = 'supprimer_produit_admin';
    final public const MODIFIER = 'modifier_produit_admin';
    final public const LISTER = 'produits_admin';
    final public const RECHERCHER = 'rechercher_agent';
    final public const CONSULTER = 'consulter_agent';
    final public const CREER = 'produit_creer_admin';

    public function __construct(Security $security, AuthorizationCheckerInterface $roleService)
    {
        $this->security = $security;
        $this->roleService = $roleService;
    }

    protected function supports($attribute, $subject): bool
    {
        // Récupération de toutes les constantes de la classe
        $reflector = new \ReflectionClass(self::class);
        $constantList = $reflector->getConstants();

        // Si l'attribut n'est pas supporté, return false
        if (!in_array($attribute, $constantList)) {
            return false;
        }

        // Si l'objet n'est pas de type Agent, il n'est pas supporté
        if ($subject && !$subject instanceof Produit) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $utilisateurConnecte = $this->security->getUser();
dd($utilisateurConnecte);

        if (!$utilisateurConnecte instanceof Utilisateur) {
            return false;
        }

        // Handle attributes that don't require a Produit
        if (in_array($attribute, [self::LISTER, self::RECHERCHER, self::CONSULTER, self::CREER])) {
            return match ($attribute) {
                self::LISTER => $this->peutLister($utilisateurConnecte),
                self::RECHERCHER => $this->peutRechercher($utilisateurConnecte),
                self::CONSULTER => $this->peutConsulter($utilisateurConnecte),
                self::CREER => $this->peutCreer($utilisateurConnecte),
                default => false,
            };
        }

        // For actions that require a Produit, ensure it's present
        if (!$subject instanceof Produit) {
            return false;
        }

        return match ($attribute) {
            self::SUPPRIMER => $this->peutSupprimer($utilisateurConnecte, $subject),
            self::MODIFIER => $this->peutModifier($utilisateurConnecte, $subject),
            default => throw new LogicException(
                "Erreur de logique dans AdminProduitVoter : type d'accès " . $attribute . ' non géré !'
            ),
        };
    }


    private function peutModifier(Utilisateur $utilisateur): bool
    {

        if ($this->roleService->isGranted(EnumRole::ROLEADMINVIP, $utilisateur)) {
            return true;
        }

        // Dans tous les autres cas, on refuse l'accès
        return false;
    }


    
    private function peutCreer(Utilisateur $utilisateur): bool
    {
        if ($this->roleService->isGranted('ROLE_ADMIN_VIP', $utilisateur) || $this->roleService->isGranted('ROLE_ADMIN', $utilisateur)) {
            return true;
        }

        // Dans tous les autres cas, on refuse l'accès
        return false;
    }


    private function peutSupprimer(Utilisateur $utilisateur, Produit $produit): bool
    {
        if ($this->roleService->isGranted(EnumRole::ROLEADMINVIP, $utilisateur)) {
            return true;
        }


        // Dans tous les autres cas, on refuse l'accès
        return false;
    }

    private function peutLister(Utilisateur $utilisateur): bool
    {
        if ($utilisateur->hasRole('ROLE_ADMIN_VIP') || $utilisateur->hasRole('ROLE_MIN') || $utilisateur->hasRole('ROLE_ADMIN')) {
            return true;
        }

        return false;
    }

    private function peutRechercher(Utilisateur $utilisateur): bool
    {
        if ($utilisateur->hasRole('ROLE_ADMIN_VIP')) {
            return false;
        }

        return true;
    }

    private function peutConsulter(Utilisateur $utilisateur): bool
    {
        if ($utilisateur->hasRole('ROLE_ADMIN_VIP') || ('ROLE_AGENT')) {
            return false;
        }

        return true;
    }
}
