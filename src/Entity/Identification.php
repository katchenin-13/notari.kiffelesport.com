<?php

namespace App\Entity;

use App\Repository\IdentificationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: IdentificationRepository::class)]
class Identification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;



    #[ORM\ManyToOne(targetEntity: Dossier::class, inversedBy: 'identifications',)]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    private $dossier;
 

    #[ORM\ManyToOne(inversedBy: 'identifications')]
    private ?Client $clients = null;

    #[ORM\Column(length: 255)]
    private ?string $attribut= null;

    #[ORM\ManyToOne(inversedBy: 'identifications')]
    private ?TypeClient $type = null;

    #[ORM\Column(length: 255)]
    private ?string $montant = null;


    public function getId(): ?int
    {
        return $this->id;
    }


    // public function getVendeur(): ?Client
    // {
    //     return $this->vendeur;
    // }

    // public function setVendeur(?Client $vendeur): self
    // {
    //     $this->vendeur = $vendeur;

    //     return $this;
    // }

    public function getDossier(): ?Dossier
    {
        return $this->dossier;
    }

    public function setDossier(?Dossier $dossier): self
    {
        $this->dossier = $dossier;

        return $this;
    }

    // public function getAcheteur(): ?Client
    // {
    //     return $this->acheteur;
    // }

    // public function setAcheteur(?Client $acheteur): self
    // {
    //     $this->acheteur = $acheteur;

    //     return $this;
    // }
    public function getAttribut(): ?string
    {
        return $this->attribut;
    }

    public function setAttribut(string $attribut): self
    {
        $this->attribut = $attribut;

        return $this;
    }

    public function getClients(): ?Client
    {
        return $this->clients;
    }

        public function setClients(?Client $clients): self
    {
        $this->clients = $clients;

        return $this;
    }

    public function getType(): ?TypeClient
    {
        return $this->type;
    }

    public function setType(?TypeClient $type): self
    {
        $this->type = $type;

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
}
