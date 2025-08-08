<?php

namespace App\Manager;

use App\Entity\FAQ;
use Doctrine\ORM\EntityManagerInterface;

class FAQManager
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {}

    /**
     * Get all FAQ contacts.
     */
    public function listeContact(): array
    {
        $contacts = $this->em->getRepository(FAQ::class)->findAll();

        $contactMessages = [];
        foreach ($contacts as $contact) {
            $contactMessages[] = [
                ucfirst($contact->getName()),
                ucwords($contact->getEmail())
            ];
        }

        return $contactMessages;
    }

    /**
     * Create a new FAQ message.
     */
    public function create(FAQ $faqMessage): ?FAQ
    {
        // Sanitize the FAQ answer (ensure no HTML is inserted)
        $sanitizedAnswer = htmlspecialchars($faqMessage->getAnswer(), ENT_QUOTES, 'UTF-8');
        $faqMessage->setAnswer($sanitizedAnswer);

        // Check if the FAQ entry already exists
        $existingFAQ = $this->em->getRepository(FAQ::class)->findOneBy([
            'question' => $faqMessage->getQuestion(),
            'answer' => $faqMessage->getAnswer(),
        ]);

        if ($existingFAQ) {
            // Return null if the FAQ entry already exists
            return null;
        }

        // Persist and flush new FAQ entry
        $this->em->persist($faqMessage);
        $this->em->flush();

        return $faqMessage; // Return the created FAQ entity
    }

    /**
     * Update an existing FAQ message.
     */
    public function update(FAQ $faqMessage): ?FAQ
    {
        // Ensure the FAQ message exists by ID
        $existingFAQ = $this->em->getRepository(FAQ::class)->find($faqMessage->getId());

        if (!$existingFAQ) {
            // If no FAQ exists with this ID, return null
            return null;
        }

        // Update the fields you want (e.g., answer and question)
        $existingFAQ->setAnswer($faqMessage->getAnswer());
        $existingFAQ->setQuestion($faqMessage->getQuestion());

        // Persist and flush the updated FAQ
        $this->em->persist($existingFAQ);
        $this->em->flush();

        return $existingFAQ; // Return the updated FAQ entity
    }

    /**
     * Delete an existing FAQ message.
     */
    public function delete(FAQ $faqMessage): void
    {
        // Ensure the FAQ exists before deleting
        $existingFAQ = $this->em->getRepository(FAQ::class)->find($faqMessage->getId());

        if ($existingFAQ) {
            $this->em->remove($existingFAQ);
            $this->em->flush();
        }
    }
}
