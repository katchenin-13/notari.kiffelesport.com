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

    #[ORM\OneToMany(mappedBy: 'fournisseurs', targetEntity: CompteFournisseur::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $compteFournisseurs;

 
    public function __construct()
    {
        $this->marches = new ArrayCollection();
        $this->compteFournisseurs = new ArrayCollection();
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
     * @return Collection<int, Marche>
     */
    public function getMarches(): Collection
    {
        return $this->marches;
    }

    public function addMarch(Marche $march): static
    {
        if (!$this->marches->contains($march)) {
            $this->marches->add($march);
            $march->setFournisseur($this);
        }

        return $this;
    }

    public function removeMarch(Marche $march): static
    {
        if ($this->marches->removeElement($march)) {
            // set the owning side to null (unless already changed)
            if ($march->getFournisseur() === $this) {
                $march->setFournisseur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CompteFournisseur>
     */
    public function getCompteFournisseurs(): Collection
    {
        return $this->compteFournisseurs;
    }

    public function addCompteFournisseur(CompteFournisseur $compteFournisseur): static
    {
        if (!$this->compteFournisseurs->contains($compteFournisseur)) {
            $this->compteFournisseurs->add($compteFournisseur);
            $compteFournisseur->setFournisseurs($this);
        }

        return $this;
    }

    public function removeCompteFournisseur(CompteFournisseur $compteFournisseur): static
    {
        if ($this->compteFournisseurs->removeElement($compteFournisseur)) {
            // set the owning side to null (unless already changed)
            if ($compteFournisseur->getFournisseurs() === $this) {
                $compteFournisseur->setFournisseurs(null);
            }
        }

        return $this;
    }

 
   
   
}
