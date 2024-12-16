<?php

namespace App\Entity;

use App\Repository\PaiementFraisRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: PaiementFraisRepository::class)]
class PaiementFrais
{
   

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $montant;

    

    #[ORM\Column(length: 255)]
    private ?string $attribut = null;


    #[ORM\ManyToOne(targetEntity: Dossier::class, inversedBy: 'paiementFrais')]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    private $dossier;

    #[ORM\ManyToOne(inversedBy: 'paiementFrais')]
    private ?Client $client = null;

    

    public function getId(): ?int
    {
        return $this->id;
    }

    
    public function getAttribut(): ?string
    {
        return $this->attribut;
    }

    public function setAttribut(string $attribut): static
    {
        $this->attribut = $attribut;

        return $this;
    }




    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDossier(): ?Dossier
    {
        return $this->dossier;
    }

    public function setDossier(?Dossier $dossier): self
    {
        $this->dossier = $dossier;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }
}
