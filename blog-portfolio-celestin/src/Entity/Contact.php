<?php

namespace App\Entity;

use App\Repository\ContactRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Interfaces\GenericTraitInterface; // ✅ Corrigé : sans le "s"
use App\Traits\GenericTrait;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact implements GenericTraitInterface // ✅ Corrigé ici aussi
{
    use GenericTrait;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Name should not be blank.')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Name cannot exceed {{ limit }} characters.'
    )]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Message should not be blank.')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Message cannot exceed {{ limit }} characters.'
    )]
    private ?string $message = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Email should not be blank.')]
    #[Assert\Email(message: 'The email {{ value }} is not a valid email.')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Email cannot exceed {{ limit }} characters.'
    )]
    private ?string $email = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

}
