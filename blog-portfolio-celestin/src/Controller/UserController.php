<?php

namespace App\Controller;

use App\Entity\User;
use App\Manager\UserManager;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Security\UserVoter;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api/utilisateur', name: 'api_utilisateur_')]
class UserController extends AbstractController
{

    public function __construct
    (
        private  readonly UserManager $utilisateurManager,
        private  readonly  SerializerInterface $serializer,
    )
    {

    }

    #[IsGranted(UserVoter::SEE_COMPTE_USER)]
    #[Route(path: '/{id}',name: "show", methods: ['GET'])]
    public function show(
        int $id,
        UserRepository $utilisateurRepository,
        SerializerInterface $serializer
    ): JsonResponse {
        $utilisateur = $utilisateurRepository->find($id);

        if (!$utilisateur) {
            return new JsonResponse(['error' => 'User non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $json = $serializer->serialize($utilisateur, 'json', ['groups' => 'utilisateur:read']);

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route(path: '/create', name: 'utilisateur_create', methods: ['POST'])]
    #[IsGranted(UserVoter::CREATE_COMPTE_USER)]
    public function create(
        Request $request,
        string $confirmeToken
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $utilisateur = new User();
        $form = $this->createForm(UserType::class, $utilisateur);
        $form->submit($data);

        if (!$form->isValid()) {
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }
        $role = $form->get('role')->getData();
        $this->utilisateurManager->peutFaireActionAdmin($utilisateur, $role);
        $this->utilisateurManager->creerUser($utilisateur, $confirmeToken, $role);

        $json = $this->serializer->serialize($utilisateur, 'json', ['groups' => 'utilisateur:read']);

        return new JsonResponse($json, Response::HTTP_CREATED, [], true);
    }


    #[IsGranted(UserVoter::SEE_COMPTE_USER)]
    #[Route(path: '/edit/{id}',name: "edit", methods: ['GET'])]
    public function edit(
        SerializerInterface $serializer,
        string $confirmeToken,
        string $confirmationToken,
        User $utilisateur,
    ): JsonResponse {

        if (!$utilisateur) {
            return new JsonResponse(['error' => 'User non trouvé'], Response::HTTP_NOT_FOUND);
        }
        $this->utilisateurManager->editeUser($utilisateur, $confirmeToken, $confirmationToken);
        $json = $serializer->serialize($utilisateur, 'json', ['groups' => 'utilisateur:read']);

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

}
