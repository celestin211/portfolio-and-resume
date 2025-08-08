<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Service\RoleService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class UserVoter extends Voter
{
    protected EntityManagerInterface $em;
    private Security $security;
    private  $roleService;

    // Liste des actions supportées
    final public const ACTIVER_ET_REDEFINIR_MOT_DE_PASSE = 'active_et_redefinir_mot_passe';
    final public const REDEFINIR_MOT_DE_PASSE = 'redefinir_mot_de_passe';
    final public const UPDATE_COMPTE_USER = 'user_edit';
    final public const DELETE_COMPTE_USER = 'user_enable';
    final public const SEE_COMPTE_USER = 'user_index';
    final public const CREATE_COMPTE_USER = 'user_new';
    public function __construct(EntityManagerInterface $em, Security $security, RoleService $roleService)
    {
        $this->em = $em;
        $this->security = $security;
        $this->roleService = $roleService;
    }

    protected function supports($attribute, $subject): bool
    {
        // Si l'attribut n'est pas supporté, return false
        if (!in_array($attribute, [
            self::ACTIVER_ET_REDEFINIR_MOT_DE_PASSE,
            self::REDEFINIR_MOT_DE_PASSE,
            self::UPDATE_COMPTE_USER,
            self::DELETE_COMPTE_USER,
            self::SEE_COMPTE_USER,
            self::CREATE_COMPTE_USER,
        ])) {
            return false;
        }

        // Si l'objet n'est pas de type Utilisateur, il n'est pas supporté
        if ($subject && !$subject instanceof User) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $userConnecte = $this->security->getUser();

        if (!$userConnecte instanceof User) {
            // Si l'utilisateur n'est pas connecté, l'accès est refusé
            return false;
        }

        /* @var User $user */
        $utilisateur = $subject;

        switch ($attribute) {

            case self::DELETE_COMPTE_USER:
                return $this->canDelete();

            case self::UPDATE_COMPTE_USER:
                return $this->canUpdate($userConnecte);

            case  self::CREATE_COMPTE_USER:
                return $this->canCreateUser($user, $userConnecte);

            case self::SEE_COMPTE_USER:
                return $this->canSeeUser();
        }

        throw new \LogicException("Erreur de logique dans UserVoter : type d'accès ".$attribute.' non géré !');
    }


    private function canSeeUser(User $user): bool
    {
        if ($this->roleService->isGranted('ROLE_ADMIN_VIP', $user)) {
            return true;
        }

        // Dans tous les autres cas, on refuse l'accès
        return false;
    }

    private function canUpdate(?User $user): bool
    {
        if ($this->roleService->isGranted('ROLE_ADMIN', $user) || $this->roleService->isGranted('ROLE_ADMIN_VIP',$user)) {
            return true;
        }

        // Dans tous les autres cas, on refuse l'accès
        return false;
    }

    private function canDelete(?User $user): bool
    {
        if ($this->roleService->isGranted('ROLE_ADMIN_VIP', $user)){
            return true;
        }
            return false;
        }
    private function canCreateUser(User $utilisateur)
    {
        if($this->roleService->isGranted('ROLE_AMDIN_VIP', $utilisateur) || $this->roleService->isGranted('ROLE_ADMIN', $utilisateur) ){
            return true;
        }
        return false;
    }

}
