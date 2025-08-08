<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\Produit;
use App\Entity\Utilisateur;
use LogicException;
use Symfony\Component\HttpKernel\Exception\LengthRequiredHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use App\EnumTypes\EnumRole;

class ProduitVoter extends Voter
{
    private Security $security;
    private AuthorizationCheckerInterface $roleService;

    // Liste des actions supportées

    final public const LISTER = 'produits_users';
    final public const RECHERCHER = 'rechercher_produit';

    final public const CONSULTER = 'consulter_produit';


    public function __construct(Security $security, AuthorizationCheckerInterface $roleService)
    {
        $this->security = $security;
        $this->roleService = $roleService;
    }

    protected function supports($attribute, $subject): bool
    {
        // Si l'attribut n'est pas supporté, return false
        if (!in_array($attribute, [
            self::CONSULTER,
            self::RECHERCHER,
            self::LISTER,

        ])) {
            return false;
        }

        if(is_array($subject) && isset($subject['greve']) && isset($subject['service']) && isset($subject['jourGreve'])) {
            return true;
        }

        // Si l'objet n'est pas de type Greve, il n'est pas supporté
        if ($subject && !$subject instanceof Produit) {
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

        /* @var Produit $produit */
        $produit = $subject;

        return match ($attribute) {
     
            self::LISTER => $this->peutLister($produit),
            self::RECHERCHER => $this->peutRechercher($produit),
            self::CONSULTER => $this->peutConsulter($produit),
       

        };
    }

   

    private function peutCreerFacture(Produit $produit): bool
    {

        if ($this->security->isGranted(EnumRole::ROLEADMINVIP)
        ) {
            return true;
        }

        return false;
    }

   
    private function peutLister(Produit $produit): bool
    {
        if ($this->security->isGranted(EnumRole::ROLEADMINVIP)
        ) {
            return true;
        }

        return false;
    }

    private function peutRechercher(Produit $produit): bool
    {
        if ($this->security->isGranted(EnumRole::ROLEADMINVIP)
        ) {
            return true;
        }

        return false;
    }

    private function peutConsulter(Produit $produit): bool
    {
        if ($this->security->isGranted(EnumRole::ROLEADMINVIP)
        ) {
            return true;
        }

        return false;
    }

}
