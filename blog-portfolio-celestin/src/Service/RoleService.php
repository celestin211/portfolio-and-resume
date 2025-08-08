<?php

namespace App\Service;

use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

//  Vérifier si un utilisateur a bien un role précis, en tenant compte de la hiérarchie des rôles.
//  isGranted vérifie le role de l'utilisateur connecté uniquement.

class RoleService
{
    private $roleHierarchy;

    /**
     * Constructor.
     */
    public function __construct(RoleHierarchyInterface $roleHierarchy)
    {
        $this->roleHierarchy = $roleHierarchy;
    }

    public function isGranted($role, $user)
    {
        foreach ($user->getRoles() as $userRole) {
            if (in_array($role, $this->roleHierarchy->getReachableRoleNames([$userRole]))) {
                return true;
            }
        }
        return false;
    }

}
