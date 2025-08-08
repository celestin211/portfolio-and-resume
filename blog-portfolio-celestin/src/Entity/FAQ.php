<?php

namespace App\Entity;

use App\Interfaces\GenericTraitInterface;
use App\Repository\FAQRepository;
use App\Traits\GenericTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Attributes as OA;


#[ORM\Entity(repositoryClass: FAQRepository::class)]
class FAQ implements GenericTraitInterface
{

    use GenericTrait;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Question is required')]
    #[Groups(["faq:read", "faq:write"])]
    #[OA\Property(description: "The question for the FAQ.")]
    private ?string $question = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Answer is required')]
    #[Groups(["faq:read", "faq:write"])]
    #[OA\Property(description: "The answer to the FAQ question.")]
    private ?string $answer = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Groups(["faq:read", "faq:write"])]
    #[OA\Property(description: "Indicates whether the FAQ is active.")]
    private ?bool $isActive = null;

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): static
    {
        $this->question = $question;

        return $this;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): static
    {
        $this->answer = $answer;

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

}
