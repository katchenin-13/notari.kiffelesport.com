<?php

namespace App\Entity;

use App\Repository\LignedepenseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LignedepenseRepository::class)]
class Lignedepense
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'lignedepenses',targetEntity: Typedepense::class)]
    private ?Typedepense $typedepense = null;

    
    #[ORM\Column(length: 255)]
    #[ORM\JoinColumn(nullable: false)]
    private ?string $montant = null;
    // #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    // private ?string $montant = null;
    
    #[ORM\ManyToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: false)]
    private ?FichierAdmin $fichier = null;

    #[ORM\ManyToOne(inversedBy: 'lignedepenses',targetEntity: Depense::class)]
    private ?Depense $depenses = null;
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypedepense(): ?Typedepense
    {
        return $this->typedepense;
    }

    public function setTypedepense(?Typedepense $typedepense): static
    {
        $this->typedepense = $typedepense;

        return $this;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getFichier(): ?FichierAdmin
    {
        return $this->fichier;
    }

    public function setFichier(?FichierAdmin $fichier): self
    {
        $this->fichier = $fichier;

        return $this;
    }

    public function getDepenses(): ?Depense
    {
        return $this->depenses;
    }

    public function setDepenses(?Depense $depenses): static
    {
        $this->depenses = $depenses;

        return $this;
    }
}
