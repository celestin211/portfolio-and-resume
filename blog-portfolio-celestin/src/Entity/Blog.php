<?php

namespace App\Entity;

use App\Repository\BlogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Interfaces\GenericTraitInterface;
use App\Traits\GenericTrait;
use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BlogRepository::class)]
class Blog implements GenericTraitInterface
{
    use GenericTrait;


    // Other existing fields...

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Blog name is required')]
    #[Groups(["user:read", "user:write"])]
    #[Assert\Length(max: 100, maxMessage: 'The name must not exceed {{ limit }} characters.')]
    #[OA\Property(description: "The name of the blog")]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(["user:read", "user:write"])]
    #[OA\Property(description: "A description for the blog.")]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(["user:read", "user:write"])]
    #[OA\Property(description: "Date when the blog was created.")]
    private ?\DateTimeInterface $createdBlogAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(["user:read", "user:write"])]
    #[OA\Property(description: "Date when the blog was last updated.")]
    private ?\DateTimeInterface $updatedBlogAt = null;

    #[ORM\Column(length: 255)]
    #[Groups(["user:read", "user:write"])]
    #[OA\Property(description: "The image associated with the blog.")]
    private ?string $image = null;

    #[Assert\File(maxSize: '5M', mimeTypes: ['application/pdf', 'application/png', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv', 'application/msword'], mimeTypesMessage: 'Invalid file type')]
    #[ORM\Column(name: 'imageFilename', type: 'string', length: 255, nullable: true)]
    #[Groups(["user:read", "user:write"])]
    private ?string $imageFilename = null;

    /**
     * @return string|null
     */
    public function getImageFilename(): ?string
    {
        return $this->imageFilename;
    }

    /**
     * @param string|null $imageFilename
     */
    public function setImageFilename(?string $imageFilename): void
    {
        $this->imageFilename = $imageFilename;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getUpdatedBlogAt(): ?\DateTimeInterface
    {
        return $this->updatedBlogAt;
    }

    /**
     * @param \DateTimeInterface|null $updatedBlogAt
     */
    public function setUpdatedBlogAt(?\DateTimeInterface $updatedBlogAt): void
    {
        $this->updatedBlogAt = $updatedBlogAt;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCreatedBlogAt(): ?\DateTimeInterface
    {
        return $this->createdBlogAt;
    }

    /**
     * @param \DateTimeInterface|null $createdBlogAt
     */
    public function setCreatedBlogAt(?\DateTimeInterface $createdBlogAt): void
    {
        $this->createdBlogAt = $createdBlogAt;
    }

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

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }
}
