<?php

namespace App\Entity;

use App\Interfaces\GenericTraitInterface;
use App\Repository\UserRepository;
use App\Traits\GenericTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements GenericTraitInterface
{
    use GenericTrait;

    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    #[Assert\NotBlank(message: 'Name is required')]
    #[Groups(["user:read", "user:write"])]
    #[Assert\Length(max: 100, maxMessage: 'The name must not exceed {{ limit }} characters.')]
    #[OA\Property(description: "The name must be provided.")]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    #[Assert\NotBlank(message: 'Lastname is required')]
    #[Assert\Length(max: 100, maxMessage: 'The lastname must not exceed {{ limit }} characters.')]
    #[Groups(["user:read", "user:write"])]
    #[OA\Property(description: "The lastname must be provided.")]
    private ?string $lastname = null;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank(message: 'Password is required')]
    #[Groups(["user:read", "user:write"])]
    #[Assert\Length(max: 255, maxMessage: 'The password must not exceed {{ limit }} characters.')]
    #[OA\Property(description: "The password must be provided.")]
    private ?string $password = null;

    #[Assert\NotBlank(message: 'Please confirm your password')]
    #[Assert\EqualTo(propertyPath:"password", message: 'Please enter a matching password')]
    #[Groups(["user:read", "user:write"])]
    private ?string $passwordConfirm;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank(message: 'Email is required')]
    #[Groups(["user:read", "user:write"])]
    #[OA\Property(description: "The email must be provided.")]
    private ?string $email = null;

    #[ORM\Column(name: 'roles', type: 'json')]
    #[Groups(["user:read", "user:write"])]
    private array $roles = ['ROLE_USER']; // Default role

    #[Assert\File(maxSize: '5M', mimeTypes: ['application/pdf', 'application/png', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv', 'application/msword'], mimeTypesMessage: 'Invalid file type')]
    #[ORM\Column(name: 'imageFilename', type: 'string', length: 255, nullable: true)]
    #[Groups(["user:read", "user:write"])]
    private ?string $imageFilename = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $salt = null;

    #[ORM\Column]
    private ?bool $locked = null;

    #[ORM\Column]
    private ?bool $enabled = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $expiredAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $confirmationToken = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $passwordRequestedAt = null;

    #[ORM\Column]
    private ?bool $recevoirMessage = null;

    #[ORM\Column(nullable: true)]
    private ?bool $forceChangePassword = null;

    #[ORM\Column(name: 'path', type: 'string', length: 255, nullable: true)]
    #[Groups(["user:read", "user:write"])]
    private ?string $path = null;

    #[ORM\Column(name: 'filename', type: 'string', length: 255, nullable: true)]
    #[Groups(["user:read", "user:write"])]
    private ?string $filename = null;

    public function __construct()
    {
        $this->enabled = true;
        $this->locked = false;
        $this->roles = ['ROLE_USER'];  // Default role
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

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getPasswordConfirm(): ?string
    {
        return $this->passwordConfirm;
    }

    public function setPasswordConfirm(?string $passwordConfirm): static
    {
        $this->passwordConfirm = $passwordConfirm;
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

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getImageFilename(): ?string
    {
        return $this->imageFilename;
    }

    public function setImageFilename(?string $imageFilename): static
    {
        $this->imageFilename = $imageFilename;
        return $this;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function setSalt(?string $salt): static
    {
        $this->salt = $salt;
        return $this;
    }

    public function isLocked(): ?bool
    {
        return $this->locked;
    }

    public function setLocked(bool $locked): static
    {
        $this->locked = $locked;
        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;
        return $this;
    }

    public function getExpiredAt(): ?\DateTimeInterface
    {
        return $this->expiredAt;
    }

    public function setExpiredAt(?\DateTimeInterface $expiredAt): static
    {
        $this->expiredAt = $expiredAt;
        return $this;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): static
    {
        $this->confirmationToken = $confirmationToken;
        return $this;
    }

    public function getPasswordRequestedAt(): ?\DateTimeInterface
    {
        return $this->passwordRequestedAt;
    }

    public function setPasswordRequestedAt(?\DateTimeInterface $passwordRequestedAt): static
    {
        $this->passwordRequestedAt = $passwordRequestedAt;
        return $this;
    }

    public function isRecevoirMessage(): ?bool
    {
        return $this->recevoirMessage;
    }

    public function setRecevoirMessage(bool $recevoirMessage): static
    {
        $this->recevoirMessage = $recevoirMessage;
        return $this;
    }

    public function isForceChangePassword(): ?bool
    {
        return $this->forceChangePassword;
    }

    public function setForceChangePassword(?bool $forceChangePassword): static
    {
        $this->forceChangePassword = $forceChangePassword;
        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): static
    {
        $this->path = $path;
        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(?string $filename): static
    {
        $this->filename = $filename;
        return $this;
    }



    public function getUsername(): ?string
    {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function __toString()
    {
        return $this->getUsername();
    }


    public function isPasswordRequestNonExpired($ttl): bool
    {
        return $this->getPasswordRequestedAt() instanceof \DateTime &&
            $this->getPasswordRequestedAt()->getTimestamp() + $ttl > time();
    }

    public function hasRole(?string $role): bool
    {
        return in_array($role, $this->roles);
    }

    public function removeRole(?string $roleUser): static
    {
        $index = array_search($roleUser, $this->roles);
        if (false !== $index) {
            unset($this->roles[$index]);
        }

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function isEqualTo(UserInterface $user): bool
    {
        if (!$user instanceof self) {
            return false;
        }

        if ($this->getPassword() !== $user->getPassword()) {
            return false;
        }

        $currentRoles = array_map('strval', $this->getRoles());
        $newRoles = array_map('strval', (array) $user->getRoles());
        $rolesChanged = \count($currentRoles) !== \count($newRoles) || \count($currentRoles) !== \count(array_intersect($currentRoles, $newRoles));
        if ($rolesChanged) {
            return false;
        }

        if ($this->getUserIdentifier() !== $user->getUserIdentifier()) {
            return false;
        }

        if ($this->isEnabled() !== $user->isEnabled()) {
            return false;
        }

        if ($this->isLocked() !== $user->isLocked()) {
            return false;
        }

        return true;
    }

    public function getCreatedAtUser(): ?\DateTimeInterface
    {
        return $this->createdAtUser;
    }

    public function setCreatedAtUser(?\DateTimeInterface $createdAtUser): static
    {
        $this->createdAtUser = $createdAtUser;

        return $this;
    }
    /**
     * @return bool|null
     */
    public function getBoxMessage(): ?bool
    {
        return $this->boxMessage;
    }

    /**
     * @param bool|null $boxMessage
     */
    public function setBoxMessage(?bool $boxMessage): void
    {
        $this->boxMessage = $boxMessage;
    }
}
