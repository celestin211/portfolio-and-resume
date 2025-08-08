<?php

namespace App\Controller;

use App\Entity\FAQ;
use App\Manager\FAQManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;

class FAQController extends AbstractController
{

    public function __construct
    (
        readonly  FAQManager $faqManager,
        readonly  EntityManagerInterface $em,
        readonly  SerializerInterface $serializer
    )
    {

    }

    #[Route('/api/faqs', name: 'api_faq_list', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $faqs = $this->faqManager->listeContact();

        return $this->json($faqs);
    }

    #[Route('/api/faqs/{id}', name: 'api_faq_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $faq = $this->em->getRepository(FAQ::class)->find($id);

        if (!$faq) {
            throw new NotFoundHttpException('FAQ not found');
        }

        return $this->json($faq, 200, [], ['groups' => 'faq:read']);
    }

    #[Route('/api/faqs', name: 'api_faq_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $faq = new FAQ();
        $faq->setQuestion($data['question']);
        $faq->setAnswer($data['answer']);

        $createdFAQ = $this->faqManager->create($faq);

        if ($createdFAQ === null) {
            return new JsonResponse(['message' => 'FAQ already exists'], Response::HTTP_CONFLICT);
        }

        return $this->json($createdFAQ, Response::HTTP_CREATED, [], ['groups' => 'faq:read']);
    }

    #[Route('/api/faqs/{id}', name: 'api_faq_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $faq = $this->em->getRepository(FAQ::class)->find($id);

        if (!$faq) {
            throw new NotFoundHttpException('FAQ not found');
        }

        $data = json_decode($request->getContent(), true);

        $faq->setQuestion($data['question']);
        $faq->setAnswer($data['answer']);

        $updatedFAQ = $this->faqManager->update($faq);

        if ($updatedFAQ === null) {
            return new JsonResponse(['message' => 'FAQ could not be updated'], Response::HTTP_BAD_REQUEST);
        }

        return $this->json($updatedFAQ, Response::HTTP_OK, [], ['groups' => 'faq:read']);
    }

    #[Route('/api/faqs/{id}', name: 'api_faq_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $faq = $this->em->getRepository(FAQ::class)->find($id);

        if (!$faq) {
            throw new NotFoundHttpException('FAQ not found');
        }

        $this->faqManager->delete($faq);

        return new JsonResponse(['message' => 'FAQ deleted successfully'], Response::HTTP_NO_CONTENT);
    }
}
