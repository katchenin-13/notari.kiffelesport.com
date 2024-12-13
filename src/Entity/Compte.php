<?php

namespace App\Entity;

use App\Repository\CompteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Gedmo\Mapping\Annotation as Gedmo; // gedmo annotations


#[ORM\Entity(repositoryClass: CompteRepository::class)]
class Compte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'comptes')]
    private ?Client $client = null;

    #[ORM\ManyToOne(inversedBy: 'comptes')]
    private ?Dossier $dossier = null;

    #[ORM\Column(length: 255)]
    private ?string $montant = null;

    #[ORM\Column(length: 255)]
    #[ORM\JoinColumn(nullable: false)]
    private ?string $solde = null;

    // #[ORM\Column(type: 'boolean',)]
    // private $active;


    #[ORM\Column(type: 'boolean', nullable: true)]
    private $etat;

    #[ORM\OneToMany(mappedBy: 'compte', targetEntity: Ligneversementfrais::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $ligneversementfrais;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Gedmo\Timestampable(on: 'create')]
    private ?\DateTimeInterface $datecreation = null;



 
   
    public function __construct()
    {
        $this->ligneversementfrais = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDossier(): ?Dossier
    {
        return $this->dossier;
    }

    public function setDossier(?Dossier $dossier): static
    {
        $this->dossier = $dossier;

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
    // public function getActive(): ?bool
    // {
    //     return $this->active;
    // }

    // public function setActive(bool $active): self
    // {
    //     $this->active = $active;

    //     return $this;
    // }



    public function getEtat(): ?array
    {
        return $this->etat;
    }

    public function setEtat(array $etat): self
    {
        $this->etat = $etat;

        return $this;
    }


    public function getSolde(): ?string
    {
        return $this->solde;
    }

    public function setSolde(string $solde): static
    {
        $this->solde = $solde;

        return $this;
    }

    /**
     * @return Collection<int, Ligneversementfrais>
     */
    public function getLigneversementfrais(): Collection
    {
        return $this->ligneversementfrais;
    }

    public function addLigneversementfrai(Ligneversementfrais $ligneversementfrai): static
    {
        if (!$this->ligneversementfrais->contains($ligneversementfrai)) {
            $this->ligneversementfrais->add($ligneversementfrai);
            $ligneversementfrai->setCompte($this);
        }

        return $this;
    }

    public function removeLigneversementfrai(Ligneversementfrais $ligneversementfrai): static
    {
        if ($this->ligneversementfrais->removeElement($ligneversementfrai)) {
            // set the owning side to null (unless already changed)
            if ($ligneversementfrai->getCompte() === $this) {
                $ligneversementfrai->setCompte(null);
            }
        }

        return $this;
    }

    public function getDatecreation(): ?\DateTimeInterface
    {
        return $this->datecreation;
    }

    public function setDatecreation(?\DateTimeInterface $datecreation): static
    {
        $this->datecreation = $datecreation;

        return $this;
    }
  
   

}
