<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Manager\SecurityManager;
use App\Repository\ConnexionRepository;
use App\Security\AdminSecurity\AdminSecurityVoter;
use App\Security\UserVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;



class SecurityController extends AbstractController
{


    private $jwtManager;

    public function __construct
    (
        JWTTokenManagerInterface $jwtManager,
        private readonly   Security $security,
        private readonly   SecurityManager $securityManager,
        private readonly   UserRepository $userRepository,
        private readonly   UserPasswordHasherInterface $passwordHasher,
        private readonly   TokenStorageInterface $tokenStorage,
        private readonly   EntityManagerInterface $entityManager,
        private readonly   ConnexionRepository $connexionRepository,
        private readonly   SerializerInterface $serializer
    )
    {
        $this->jwtManager = $jwtManager;


    }

    #[Route(path: '/api/login', name: 'login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        $utilisteur = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if (!$utilisteur || !$this->passwordHasher->isPasswordValid($utilisteur, $password)) {
            return new JsonResponse(['error' => 'Invalid email or password'], 401);
        }

        $token = $this->jwtManager->create($utilisteur);

        return new JsonResponse([
            'token' => $token,
            'name' => $utilisteur->getName(),
            'lastname' => $utilisteur->getLastName(),
            'email' => $utilisteur->getEmail(),
        ]);
    }



    #[Route(path: '/api/current_utilisateur', name: 'current_utilisateur', methods: ['POST'])]
    public function getCurrentUser(Request $request): JsonResponse
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        $utilisteur = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$utilisteur) {
            return new JsonResponse(['error' => 'User introuvable'], 404);
        }

        if (!$this->passwordHasher->isPasswordValid($utilisteur, $password)) {
            return new JsonResponse(['error' => 'Email ou mot de passe incorrect'], 401);
        }

        $token = $this->jwtManager->create($utilisteur);

        if ($token) {
            return new JsonResponse([
                'token' => $token,
                'username' => $utilisteur->getUsername(),
                'email' => $utilisteur->getEmail(),
                'roles' => $utilisteur->getRoles(),
            ]);
        }

        return new JsonResponse(['error' => 'Authentification échouée'], 401);
    }


    #[Route(path: '/api/logout_user', name: 'logout_user', methods: ['GET', 'POST'])]
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }


    #[IsGranted(UserVoter::REDEFINIR_MOT_DE_PASSE)]
    #[Route(path: '/api/change-password', name: 'api_utilisateur_change_password', methods: ['POST'])]
    public function apiChangePassword(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['newPassword'])) {
            return new JsonResponse([
                'error' => 'Le champ "newPassword" est requis.'
            ], Response::HTTP_BAD_REQUEST);
        }

        /** @var User $user */
        $user = $this->security->getUser();


        try {
            $this->securityManager->changePassword($user, $data['newPassword']);

            return new JsonResponse([
                'message' => 'Mot de passe modifié avec succès.'
            ], Response::HTTP_OK);
        }

        catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Une erreur est survenue lors de la modification du mot de passe.',
                'details' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[IsGranted(UserVoter::ACTIVER_ET_REDEFINIR_MOT_DE_PASSE)]
    #[Route(path: '/api/demande-reinitialisation-mot-de-passe', name: 'demande_reinitialisation_mot_de_passe', methods: ['POST'])]
    public function apiDemandeReinitialisationMotDePasse(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['email'])) {
            return new JsonResponse([
                'error' => 'L\'adresse email est requise.'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->securityManager->demandeReinitialisationMotDePasse($data['email']);

            return new JsonResponse([
                'message' => "Un email de réinitialisation de mot de passe a été envoyé à l'adresse ".$data['email']
            ], Response::HTTP_OK);

        }

        catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la demande de réinitialisation.',
                'details' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/api/mot-de-passe/{confirmationToken}', name: 'mot_de_passe_reset_or_init', methods: ['POST'])]
    public function apiInitialisationReinitialisationMotDePasse(
        Request $request,
        string $confirmationToken,
        EntityManagerInterface $em
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['newPassword']) || empty($data['newPassword'])) {
            return new JsonResponse([
                'error' => 'Le nouveau mot de passe est requis.'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $user = $em->getRepository(User::class)->findOneBy(['confirmationToken' => $confirmationToken]);

            if (!$user) {
                return new JsonResponse([
                    'error' => 'Token de confirmation invalide ou expiré.'
                ], Response::HTTP_NOT_FOUND);
            }

            $this->securityManager->changePassword($user, $data['newPassword']);
            $user->setConfirmationToken(null); // Optionnel : vider le token après usage

            return new JsonResponse([
                'message' => 'Mot de passe mis à jour avec succès.'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Erreur lors de la mise à jour du mot de passe.',
                'details' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route(path: '/authenticated', name: 'api_user_authenticated', methods: ['GET'])]
    public function isAuthenticated() : JsonResponse
    {
        if (!$this->security->getUser()){
            $response = [
                "success"=>false,
                "message"=>"L'utilisateur n'a pas de session",
            ];
            return new JsonResponse($response,Response::HTTP_OK);
        } else {
            $user = $this->userRepository->findOneBy(['username'=>$this->security->getUser()->getUsername()]);
            $response = [
                "success"=>true,
                "message"=>"Usuario logueado",
                "errors"=> [],
                "results"=>['id'=>$user->getId()]
            ];
            if (in_array('ROLE_ADMIN', $user->getRoles()))
                $response["is_admin"] = true;

            return new JsonResponse($response,Response::HTTP_OK);
        }
    }

    #[IsGranted(AdminSecurityVoter::CONSULTER_PROFILE)]
    #[Route(path: 'api/consulter_profile', name: 'consulter_profile')]
    public function profil(Security $security, Request $request): Response
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        $utilisteur = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$utilisteur) {
            return new JsonResponse(['error' => 'User introuvable'], 404);
        }

        if (!$this->passwordHasher->isPasswordValid($utilisteur, $password)) {
            return new JsonResponse(['error' => 'Email ou mot de passe incorrect'], 401);
        }

        $token = $this->jwtManager->create($utilisteur);

        if ($token) {
            return new JsonResponse([
                'token' => $token,
                'username' => $utilisteur->getUsername(),
                'email' => $utilisteur->getEmail(),
                'roles' => $utilisteur->getRoles(),
            ]);
        }

        return new JsonResponse(['error' => 'Authentification échouée'], 401);
    }


}
