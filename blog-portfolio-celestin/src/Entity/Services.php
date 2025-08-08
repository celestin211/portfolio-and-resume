<?php

namespace App\Entity;

use App\Interfaces\GenericTraitInterface;
use App\Repository\ServicesRepository;
use App\Traits\GenericTrait;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ServicesRepository::class)]
class Services implements GenericTraitInterface
{
   use GenericTrait;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Name is required')]
    #[Groups(["service:read", "service:write"])]
    #[OA\Property(description: "The name of the service.")]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Description is required')]
    #[Groups(["service:read", "service:write"])]
    #[OA\Property(description: "A description of the service.")]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Value is required')]
    #[Groups(["service:read", "service:write"])]
    #[OA\Property(description: "The value of the service.")]
    private ?string $value = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(["service:read", "service:write"])]
    #[OA\Property(description: "The unit of measurement for the service (optional).")]
    private ?string $unit = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(?string $unit): static
    {
        $this->unit = $unit;

        return $this;
    }
}
