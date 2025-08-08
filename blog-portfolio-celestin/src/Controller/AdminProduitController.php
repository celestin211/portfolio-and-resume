<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Blog;
use App\Manager\ProduitManager;
use App\Repository\ProduitRepository;
use App\Security\AdminSecurity\AdminProduitVoter;
use App\Security\BlogVoter;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Util\Util;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Security;

class AdminProduitController extends AbstractController
{
    private readonly JWTTokenManagerInterface $jwtManager;

    public function __construct(
        private readonly ValidatorInterface $validator,
        JWTTokenManagerInterface $jwtManager,
        private readonly ProduitManager $produitManager,
        private readonly EntityManagerInterface $em,
        private readonly ProduitRepository $produitRepository,
        private readonly SerializerInterface $serializer,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly Security $security
    ) {
        $this->jwtManager = $jwtManager;
    }

    /**
     * Crée un nouveau produit.
     *
     * @OA\Post(
     *     path="api/produit_creer_admin",
     *     summary="Créer un produit",
     *     @OA\RequestBody(
     *         @OA\JsonContent(ref=@Model(type=Produit::class, groups={"produit:write"}))
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Produit créé",
     *         @OA\JsonContent(ref=@Model(type=Produit::class, groups={"produit:read"}))
     *     ),
     *     @OA\Response(response=400, description="Erreur de validation")
     * )
     */
    #[IsGranted(AdminProduitVoter::CREER)]
#[Route('api/produit_creer_admin', name: 'produit_creer_admin', methods: ['POST', 'GET'])]
public function create(Request $request): JsonResponse
{
    // 1. Récupérer les champs texte
    $data = $request->request->all();

    // 2. Récupérer le fichier image
    $imageFile = $request->files->get('image');

    // 3. Vérification des champs obligatoires
    $required = ['nom', 'prix', 'quantite', 'categorie'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            return new JsonResponse(['error' => "Le champ $field est obligatoire"], Response::HTTP_BAD_REQUEST);
        }
    }

    // 4. Création de l'entité Produit
    $produit = new Blog();
    $produit->setNom($data['nom']);
    $produit->setPrix($data['prix']);
    $produit->setQuantite($data['quantite']);
    $produit->setCategorie($data['categorie']);
    $produit->setDescription($data['description'] ?? null);
    $produit->setWeight($data['weight'] ?? null);
    $produit->setColor($data['color'] ?? null);
    $produit->setCollection($data['collection'] ?? null);
    $produit->setSku($data['sku'] ?? null);
    $produit->setTags(isset($data['tags']) ? explode(',', $data['tags']) : []);
    $produit->setShopify($data['shopify'] ?? null);
    $produit->setFacebook($data['facebook'] ?? null);
    $produit->setInstagram($data['instagram'] ?? null);
    $produit->setCurrency($data['currency'] ?? 'EUR');
    // Ajoute ici les autres setters selon ton entité

    // 5. Gestion de l'image (si présente)
    if ($imageFile) {
        $imageName = $this->fileUploader->upload($imageFile);
        $produit->setImageFileName($imageName);
    }

    // 6. Validation
    $errors = $this->validator->validate($produit);
    if (count($errors) > 0) {
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[$error->getPropertyPath()] = $error->getMessage();
        }
        return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
    }

    // 7. Persistance
    $this->em->persist($produit);
    $this->em->flush();

    // 8. Sérialisation pour la réponse
    $data = $this->serializer->normalize($produit, null, ['groups' => 'blog:read']);

    return new JsonResponse($data, Response::HTTP_CREATED);
}


    #[Route('/{id}', name: 'delete_blog_admin', methods: ['DELETE', 'GET'])]
    #[IsGranted(AdminProduitVoter::SUPPRIMER)]
    public function delete(int $id, Request $request, SerializerInterface $serializer): JsonResponse
    {
        $produit = $this->em->getRepository(Blog::class)->find($id);

        if (!$produit) {
            return new JsonResponse(['message' => 'Blog non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $this->produitManager->delete($produit);
        $data = $serializer->normalize($produit, null, ['groups' => 'blog:write']);

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/api/creer_produit_admin', name: 'creer_blog_admin', methods: ['POST'])]
//    #[IsGranted(AdminProduitVoter::MODIFIER)]
    public function edit
    (
        int $id, Request $request,
    ): JsonResponse
    {
        $produit = $this->em->getRepository(Blog::class)->find($id);

        if (!$produit) {
            return new JsonResponse(['message' => 'Blog not found'], Response::HTTP_NOT_FOUND);
        }

        // Validation de l'entité Blog
        $errors = $this->validator->validate($produit);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        // Mise à jour du produit
        $this->produitManager->update($produit);
        $data = $this->serializer->normalize($produit, null, ['groups' => 'blog:write']);

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * Liste tous les Blog.
     *
     * @OA\Get(
     *     path="/api/blog_admin",
     *     summary="Liste des produits",
     *     @OA\Response(
     *         response=200,
     *         description="Liste des produits",
     *         @OA\JsonContent(type="array", @OA\Items(ref=@Model(type=Blog::class, groups={"produit:read"})))
     *     )
     * )
     */
    #[Route('api/produits_admin', name: 'produits_admin', methods: ['GET'])]
    #[IsGranted(AdminBlogVoter::LISTER)]
    public function liste(): JsonResponse
    {
        $produits = $this->produitManager->listeProduit();
        $data = $this->serializer->normalize($produits, null, ['groups' => 'blog:read']);
        return new JsonResponse($data);
    }

    #[Route('api/pagination', name: 'blog_pagination', methods: ['GET', 'POST'])]
    public function pagination(Request $request): JsonResponse
    {
        $draw = $request->get("draw", 1);
        $start = $request->get("start", 0);
        $length = $request->get("length", 10);
        $search = $request->get("search", ["value" => ""])["value"];
        $order = $request->get("order", [["column" => 1, "dir" => "asc"]]);
        $prix = $request->get("prix");
        $searchListeProduits = $request->get("allblog", false);

        // Pagination et recherche
        $nbProduits = $this->produitRepository->searchCount(
            $search,
            $prix,
            $searchListeProduits
        );

        $lignes = $this->produitRepository->searchPaginated(
            $search,
            $start,
            $length,
            $order,
            $prix,
            $searchListeProduits
        );

        $data = [];
        foreach ($lignes as $produit) {
            $data[] = [
                ucfirst(Util::twig_lower($produit->getNom())),
                Util::twig_upper($produit->getDescription()),
                Util::twig_title($produit->getPrix()),
                $this->render("Agent/_include/action_agent.html.twig", [
                    "produit" => $produit,
                ])->getContent(),
            ];
        }

        $response = [
            "draw" => $draw,
            "recordsTotal" => $nbProduits,
            "recordsFiltered" => $nbProduits,
            "data" => $data,
        ];

        return $this->json($response);
    }

    private function createDeleteForm($id): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('produit_delete', ['id' => $id]))
            ->setMethod('DELETE')
            ->getForm();
    }
}
