<?php

namespace App\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'fournisseur')]
class Fournisseur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255)]
    private ?string $contact = null;

    #[ORM\OneToMany(mappedBy: 'fournisseur', targetEntity: Marche::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $marches;

    #[ORM\OneToMany(mappedBy: 'fournisseur', targetEntity: Comptefour::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $comptefour;

 
    public function __construct()
    {
        $this->marches = new ArrayCollection();
        $this->comptefour = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(string $contact): static
    {
        $this->contact = $contact;

        return $this;
    }
   
    /**
     * @return Collection<int, CompteFournisseur>
     */
    public function getCompteFournisseur(): Collection
    {
        return $this->comptefour;
    }

    public function addCompteFournisseur(CompteFour $comptefour): static
    {
        if (!$this->comptefour->contains($comptefour)) {
            $this->comptefour->add($comptefour);
            $comptefour->setFournisseurs($this);
        }

        return $this;
    }

    public function removeCompteFournisseur(CompteFour $comptefour): static
    {
        if ($this->comptefour->removeElement($comptefour)) {
            // set the owning side to null (unless already changed)
            if ($comptefour->getFournisseurs() === $this) {
                $comptefour->setFournisseurs(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection<int, CompteFournisseur>
     */
    public function getMarche(): Collection
    {
        return $this->marches;
    }

    public function addMarche(Marche $marche): static
    {
        if (!$this->marches->contains($marche)) {
            $this->marches->add($marche);
            $marche->setFournisseur($this);
        }

        return $this;
    }

    public function removeMarche(Marche $marche): static
    {
        if ($this->marches->removeElement($marche)) {
            // set the owning side to null (unless already changed)
            if ($marche->getFournisseur() === $this) {
                $marche->setFournisseur(null);
            }
        }

        return $this;
    }
   
}
