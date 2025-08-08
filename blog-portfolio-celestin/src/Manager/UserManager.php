<?php

namespace App\Manager;

use App\Entity\User;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserManager
{
    public function __construct(
        private  readonly AuthorizationCheckerInterface $roleService,
        private readonly EntityManagerInterface $em,
        private readonly SecurityManager $securityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly MailService $mailer,
    ) {

    }

    /*
     * Cette fonction évalue si l'utilisateur connecté ($utilisateurConnecte) peut effectuer une action (new/edit ...)
     *  sur l'utilisateur selon son rôle ($roleUserAction) passé en paramètre
     */
    public function peutFaireActionAdmin(User $utilisateurConnecte, $roleUserAction)
    {
        if (!in_array($roleUserAction, ['ROLE_ADMIN_VIP']) && 'ROLE_ADMIN' == $utilisateurConnecte->getRoles()[0]) {
            throw new AccessDeniedException("Accès non autorisé. L'administrateur est notifié de l'action");
        }
    }

    /*
     * Cette fonction évalue si l'utilisateur connecté ($utilisateurConnecte) peut voir
     *  l'utilisateur ($utilisateurAction) et selon son rôle passé en paramètre
     */
    public function peutVoir(User $utilisateurConnecte, User $userAction)
    {
        if ($userAction->getId() == $utilisateurConnecte->getId()) {
            return true;
        }

        if ($this->roleService->isGranted('ROLE_ADMIN', $utilisateurConnecte)) {
            return true;
        }

        // Un utilisateur dgafp ne peut pas voir un utilisateur admin ou dgafp
        if (
            $this->roleService->isGranted('ROLE_ADMIN', $utilisateurConnecte)
            && !$userAction->hasRole('ROLE_USER')
        ) {
            return true;
        }

        throw new AccessDeniedException("Accès non autorisé. L'administrateur est notifié de l'action");
    }

    public function creerUser(User $user, $role, string $confirmationToken): void
    {
        // mot de passe généré par defaut
        $motDePasse = $this->securityManager->generateToken();
        $motDePasseEncode = $this->passwordHasher->hashPassword($user, $motDePasse);

        $user->setPassword($motDePasseEncode);
        $user->setConfirmationToken($this->securityManager->generateToken());
        $user->setRoles([$role]);
        $user->setCreatedAt(new  \DateTime());
        $this->em->persist($user);
        $this->mailer->sendConfirmationEmail($user, $confirmationToken);
        $this->em->flush();
    }

    public function editeUser(User $user, $role, string $confirmationToken)
    {
        // mot de passe généré par defaut
        $motDePasse = $this->securityManager->generateToken();
        $motDePasseEncode = $this->passwordHasher->hashPassword($user, $motDePasse);

        $user->setPassword($motDePasseEncode);
        $user->setConfirmationToken($this->securityManager->generateToken());
        $user->setRoles([$role]);
        $user->setCreatedAt(new \DateTime());
        $this->em->persist($user);
        $this->mailer->sendConfirmationEmail($user, $confirmationToken);
        $this->em->flush();
    }


    public function redifinirPasswordUser( $data)
    {
        $data->setEnabled(true);
        $data->setLocked(false);
        $data->setConfirmationToken(null);
        $data->setForceChangePassword(true);
        $data->setNbConnexionKO(0);
        $newPassword = $data->getPassword(); // Si vous avez une méthode getter pour accéder au mot de passe, utilisez-la
        $data->setPassword($this->passwordHasher->hashPassword($data, $newPassword));
        $this->em->flush();
    }

    public function listeUser()
    {
        $users = $this->em->getRepository(User::class)->findAll();

        $Listeutilisateurs = [];
        foreach ($users as $user) {
            $Listeutilisateurs[] = [
                ucfirst($user->getName()),
                ucfirst($user->getLastName()),
                ucfirst($user->get()),
                ucfirst($user->getEmail()),
                $user->getTelephone(),
            ];
        }

        return $Listeutilisateurs;
    }

}
