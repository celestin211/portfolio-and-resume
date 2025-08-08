<?php

declare(strict_types=1);

namespace App\Entity;

use App\Interfaces\GenericTraitInterface;
use App\Traits\GenericTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Attributes as OA;

#[ORM\Table(name: 'connexion')]
#[ORM\Entity(repositoryClass: 'App\Repository\ConnexionRepository')]
class Connexion implements GenericTraitInterface
{
    use GenericTrait;

    #[ORM\Column(type: 'string', nullable: true)]
    #[OA\Property(description: "user lié à cette connexion.")]
    #[Groups(["document:read", "document:write"])]
    private ?string $userAgent;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[Groups(["connexion:read", "connexion:write"])]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private ?User $user;

    #[ORM\Column(name: 'date_connexion', type: 'date')]
    #[Groups(["connexion:read", "connexion:write"])]
    private ?\DateTimeInterface $dateConnexion;

    public function __construct(?User $user)
    {
        $this->setUser($user);
        $this->dateConnexion = new \DateTime();  // This ensures the date is set by default to "now"
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    /**
     * @param string|null $userAgent
     */
    public function setUserAgent(?string $userAgent): void
    {
        $this->userAgent = $userAgent;
    }
    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setDateConnexion(?\DateTimeInterface $dateConnexion): static
    {
        $this->dateConnexion = $dateConnexion;

        return $this;
    }

    public function getDateConnexion(): ?\DateTimeInterface
    {
        return $this->dateConnexion;
    }
}
