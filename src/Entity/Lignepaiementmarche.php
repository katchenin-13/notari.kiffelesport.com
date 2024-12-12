<?php

namespace App\Entity;

use App\Repository\LignepaiementmarcheRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LignepaiementmarcheRepository::class)]
class Lignepaiementmarche
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $datepaiement = null;

    #[ORM\Column(length: 255)]
    private ?string $montantverse = null;

    #[ORM\OneToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    private ?FichierAdmin $fichier = null;

    #[ORM\ManyToOne(inversedBy: 'lignepaiementmarches',targetEntity: Marche::class)]
    private ?Marche $marches = null;

    #[ORM\ManyToOne(inversedBy: 'lignepaiementmarches',targetEntity: CompteFournisseur::class)]
    private ?CompteFournisseur $comptefournisseurs = null;

 public function __construct()
    {
     
 }


 
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatepaiement(): ?\DateTimeInterface
    {
        return $this->datepaiement;
    }

    public function setDatepaiement(?\DateTimeInterface $datepaiement): static
    {
        $this->datepaiement = $datepaiement;

        return $this;
    }

    public function getMontantverse(): ?string
    {
        return $this->montantverse;
    }

    public function setMontantverse(string $montantverse): static
    {
        $this->montantverse = $montantverse;

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
    public function getMarches(): ?Marche
    {
        return $this->marches;
    }

    public function setMarches(?Marche $marches): static
    {
        $this->marches = $marches;

        return $this;
    }

    public function getComptefournisseurs(): ?CompteFournisseur
    {
        return $this->comptefournisseurs;
    }

    public function setComptefournisseurs(?CompteFournisseur $comptefournisseurs): static
    {
        $this->comptefournisseurs = $comptefournisseurs;

        return $this;
    }

    
}
