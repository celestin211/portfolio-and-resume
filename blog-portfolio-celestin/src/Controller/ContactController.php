<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Manager\ContactMessageManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ContactController extends AbstractController
{
    public function __construct(
        private readonly ContactMessageManager $contactMessageManager,
        private readonly ValidatorInterface $validator,
        private readonly MailerInterface $mailer,
    ) {}

    #[Route('/api/contact', name: 'contact', methods: ['POST'])]
    public function contact(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true);

        if (!is_array($content)) {
            return new JsonResponse(['error' => 'Invalid JSON'], 400);
        }

        // Validation des champs attendus avec les bons noms
        $constraints = new Assert\Collection([
            'name' => [new Assert\NotBlank(), new Assert\Length(max: 255)],
            'email' => [new Assert\NotBlank(), new Assert\Email(), new Assert\Length(max: 255)],
            'message' => [new Assert\NotBlank(), new Assert\Length(max: 255)],
        ]);

        $violations = $this->validator->validate($content, $constraints);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }
            return new JsonResponse(['errors' => $errors], 400);
        }

        $contactMessage = new Contact();
        $contactMessage->setName($content['name']);
        $contactMessage->setEmail($content['email']);
        $contactMessage->setMessage($content['message']);

        $email = (new Email())
            ->from($content['email'])
            ->to('jc-platon@couture.com')
            ->subject('Nouveau message depuis le formulaire de contact')
            ->text(sprintf("Message de %s\n\n%s", $content['name'], $content['message']));

        $this->mailer->send($email);
        $this->contactMessageManager->create($contactMessage);

        return new JsonResponse(['message' => 'Message envoyé avec succès !'], 201);
    }
}
