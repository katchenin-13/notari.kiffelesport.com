<?php

namespace App\Entity;

use App\Repository\CompteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Gedmo\Mapping\Annotation as Gedmo; // gedmo annotations


#[ORM\Entity(repositoryClass: CompteRepository::class)]
class CompteFournisseur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $montant = null;

    #[ORM\Column(length: 255)]
    #[ORM\JoinColumn(nullable: false)]
    private ?string $solde = null;

    #[ORM\Column(type: 'boolean')]
    #[ORM\JoinColumn(nullable: false)]
    private $active;


    #[ORM\Column(type: 'boolean', nullable: true)]
    #[ORM\JoinColumn(nullable: false)]
    private $etat;


    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Gedmo\Timestampable(on: 'create')]
    private ?\DateTimeInterface $datecreation = null;

  

    #[ORM\OneToMany(mappedBy: 'comptefournisseurs', targetEntity: Lignepaiementmarche::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $lignepaiementmarches;

    #[ORM\ManyToOne(inversedBy: 'compteFournisseurs')]
    private ?Marche $marches = null;

    #[ORM\ManyToOne(inversedBy: 'compteFournisseurs')]
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

    public function setMontant(string $montant): static
    {
        $this->montant = $montant;

        return $this;
    }
    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }



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

   

    public function getDatecreation(): ?\DateTimeInterface
    {
        return $this->datecreation;
    }

    public function setDatecreation(?\DateTimeInterface $datecreation): static
    {
        $this->datecreation = $datecreation;

        return $this;
    }

 

    /**
     * @return Collection<int, Lignepaiementmarche>
     */
    public function getLignepaiementmarches(): Collection
    {
        return $this->lignepaiementmarches;
    }

    public function addLignepaiementmarch(Lignepaiementmarche $lignepaiementmarch): static
    {
        if (!$this->lignepaiementmarches->contains($lignepaiementmarch)) {
            $this->lignepaiementmarches->add($lignepaiementmarch);
            $lignepaiementmarch->setComptefournisseurs($this);
        }

        return $this;
    }

    public function removeLignepaiementmarch(Lignepaiementmarche $lignepaiementmarch): static
    {
        if ($this->lignepaiementmarches->removeElement($lignepaiementmarch)) {
            // set the owning side to null (unless already changed)
            if ($lignepaiementmarch->getComptefournisseurs() === $this) {
                $lignepaiementmarch->setComptefournisseurs(null);
            }
        }

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

    public function getFournisseurs(): ?Fournisseur
    {
        return $this->fournisseurs;
    }

    public function setFournisseurs(?Fournisseur $fournisseurs): static
    {
        $this->fournisseurs = $fournisseurs;

        return $this;
    }

}
