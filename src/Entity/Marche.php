<?php

namespace App\Entity;

use App\Repository\MarcheRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarcheRepository::class)]
class Marche
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(length: 255)]
    private ?string $montanttotal = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\OneToMany(mappedBy: 'marche', targetEntity: Paimentmarche::class)]
    private Collection $paimentmarches;

    #[ORM\ManyToOne(inversedBy: 'marches')]
    private ?Fournisseur $fournisseur = null;

    public function __construct()
    {
        $this->paimentmarches = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getMontanttotal(): ?string
    {
        return $this->montanttotal;
    }

    public function setMontanttotal(string $montanttotal): static
    {
        $this->montanttotal = $montanttotal;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Paimentmarche>
     */
    public function getPaimentmarches(): Collection
    {
        return $this->paimentmarches;
    }

    public function addPaimentmarch(Paimentmarche $paimentmarch): static
    {
        if (!$this->paimentmarches->contains($paimentmarch)) {
            $this->paimentmarches->add($paimentmarch);
            $paimentmarch->setMarche($this);
        }

        return $this;
    }

    public function removePaimentmarch(Paimentmarche $paimentmarch): static
    {
        if ($this->paimentmarches->removeElement($paimentmarch)) {
            // set the owning side to null (unless already changed)
            if ($paimentmarch->getMarche() === $this) {
                $paimentmarch->setMarche(null);
            }
        }

        return $this;
    }

    public function getFournisseur(): ?Fournisseur
    {
        return $this->fournisseur;
    }

    public function setFournisseur(?Fournisseur $fournisseur): static
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }
}
