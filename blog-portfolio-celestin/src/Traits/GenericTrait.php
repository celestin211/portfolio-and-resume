<?php

declare(strict_types=1);

namespace App\Traits;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait GenericTrait
{
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[Groups(["default:read", "default:write"])]
    protected ?int $id = null;

    #[ORM\Column(name: 'date_creation', type: 'datetime', nullable: true)]
    #[Groups(["default:read", "default:write"])]
    protected ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(name: 'date_modification', type: 'datetime', nullable: true)]
    #[Groups(["default:read", "default:write"])]
    protected ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'cree_par_id', referencedColumnName: 'id', nullable: true)]
    #[Groups(["default:read", "default:write"])]
    protected ?User $createdBy = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'modifie_par_id', referencedColumnName: 'id', nullable: true)]
    #[Groups(["default:read", "default:write"])]
    protected ?User $updatedBy = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy = null): static
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?User $updatedBy): static
    {
        $this->updatedBy = $updatedBy;
        return $this;
    }

    public function __clone()
    {
        $this->id = null;
        $this->createdAt = null;
        $this->updatedAt = null;
        $this->createdBy = null;
        $this->updatedBy = null;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
