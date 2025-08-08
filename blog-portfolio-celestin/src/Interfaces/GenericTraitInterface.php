<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Entity\User;
use DateTimeInterface;

interface GenericTraitInterface
{
    /**
     * Get the ID
     */
    public function getId(): ?int;

    /**
     * Set createdAt
     */
    public function setCreatedAt(DateTimeInterface $createdAt): static;

    /**
     * Get createdAt
     */
    public function getCreatedAt(): ?DateTimeInterface;

    /**
     * Set updatedAt
     */
    public function setUpdatedAt(?DateTimeInterface $updatedAt): static;

    /**
     * Get updatedAt
     */
    public function getUpdatedAt(): ?DateTimeInterface;

    /**
     * Set createdBy
     */
    public function setCreatedBy(?User $createdBy = null): static;

    /**
     * Get createdBy
     */
    public function getCreatedBy(): ?User;

    /**
     * Set updatedBy
     */
    public function setUpdatedBy(?User $updatedBy): static;

    /**
     * Get updatedBy
     */
    public function getUpdatedBy(): ?User;
}
