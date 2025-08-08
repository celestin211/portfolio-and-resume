<?php

declare(strict_types=1);

namespace App\Traits;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

trait ReferentielSocieteTrait
{
    #[ORM\Column(name: 'identifiant', type: 'string', length: 5, nullable: true)]
    #[Assert\Length(max: 5, maxMessage: "L'identifiant ne doit pas dépasser {{ limit }} caractères")]
    private ?string $identifiant;

    #[ORM\Column(name: 'libelleCourt', type: 'string', length:150, nullable: true)]
    #[Assert\Length(max: 150, maxMessage: 'La valeur ne doit pas dépasser {{ limit }} caractères')]
    private ?string $libelleCourt;

    #[Assert\NotBlank(message: 'Champ obligatoire')]
    #[Assert\Length(max: 150, maxMessage: 'La valeur ne doit pas dépasser {{ limit }} caractères')]
    #[ORM\Column(name: 'libelleLong', type: 'string', length:150, nullable: true)]
    private ?string $libelleLong = null;

    #[ORM\Column(name: 'debutValidite', type: 'date', nullable: true)]
    private ?\DateTimeInterface $debutValidite;

    #[ORM\Column(name: 'finValidite', type: 'date', nullable: true)]
    private ?\DateTimeInterface $finValidite;

    #[ORM\Column(name: 'statut', type: 'string', nullable: true)]
    private ?string $statut;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $actif = true;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $ajoutManuel = false;

    public function setIdentifiant(?string $identifiant): static
    {
        $this->identifiant = $identifiant;

        return $this;
    }

    public function getIdentifiant(): ?string
    {
        return $this->identifiant;
    }

    public function setLibelleCourt(?string $libelleCourt): static
    {
        $this->libelleCourt = $libelleCourt;

        return $this;
    }

    public function getLibelleCourt(): ?string
    {
        return $this->libelleCourt;
    }

    public function setLibelleLong(?string $libelleLong = null): static
    {
        $this->libelleLong = $libelleLong;

        return $this;
    }

    public function getLibelleLong(): ?string
    {
        return $this->libelleLong;
    }

    public function setDebutValidite(?\DateTimeInterface $debutValidite = null): static
    {
        $this->debutValidite = $debutValidite;

        return $this;
    }

    public function getDebutValidite(): ?\DateTimeInterface
    {
        return $this->debutValidite;
    }

    public function setFinValidite(?\DateTimeInterface $finValidite = null): static
    {
        $this->finValidite = $finValidite;

        return $this;
    }

    public function getFinValidite(): ?\DateTimeInterface
    {
        return $this->finValidite;
    }

    public function setStatut(?string $statut = null): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setActif(?bool $actif = null): static
    {
        $this->actif = $actif;

        return $this;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setAjoutManuel(?bool $ajoutManuel = null): static
    {
        $this->ajoutManuel = $ajoutManuel;

        return $this;
    }

    public function getAjoutManuel(): ?bool
    {
        return $this->ajoutManuel;
    }
}
