<?php

namespace App\Entity;

use App\Repository\CompteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: CompteRepository::class)]
class Comptefour
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $montant = null;

    #[ORM\Column(length: 255)]
    private ?string $solde = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $etat = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Gedmo\Timestampable(on: 'create')]
    private ?\DateTimeInterface $datecreation = null;

    #[ORM\OneToMany(mappedBy: 'comptefour', targetEntity: Lignepaiementmarche::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $lignepaiementmarches;

    #[ORM\ManyToOne(inversedBy: 'comptefour')]
    private ?Marche $marches = null;

    #[ORM\ManyToOne(inversedBy: 'comptefour')]
    private ?Fournisseur $fournisseurs = null;

    public function __construct()
    {
        $this->lignepaiementmarches = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSolde(): ?string
    {
        return $this->solde;
    }

    public function setSolde(string $solde): self
    {
        $this->solde = $solde;
        return $this;
    }

    public function getEtat(): ?bool
    {
        return $this->etat;
    }

    public function setEtat(?bool $etat): self
    {
        $this->etat = $etat;
        return $this;
    }

    public function getDatecreation(): ?\DateTimeInterface
    {
        return $this->datecreation;
    }

    public function setDatecreation(?\DateTimeInterface $datecreation): self
    {
        $this->datecreation = $datecreation;
        return $this;
    }

    public function getLignepaiementmarches(): Collection
    {
        return $this->lignepaiementmarches;
    }

   

    public function getMarches(): ?Marche
    {
        return $this->marches;
    }

    public function setMarches(?Marche $marches): self
    {
        $this->marches = $marches;
        return $this;
    }

    public function getFournisseurs(): ?Fournisseur
    {
        return $this->fournisseurs;
    }

    public function setFournisseurs(?Fournisseur $fournisseurs): self
    {
        $this->fournisseurs = $fournisseurs;
        return $this;
    }
}
