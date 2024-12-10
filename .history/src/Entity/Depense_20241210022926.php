<?php

namespace App\Entity;

use App\Repository\DepenseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DepenseRepository::class)]
class Depense
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(length: 255)]
    #[ORM\JoinColumn(nullable: false)]
    private ?string $mois = null;


    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $datedepense = null;

    #[ORM\OneToMany(mappedBy: 'depense', targetEntity: Lignedepense::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $lignedepenses;

    public function __construct()
    {
        $this->lignedepenses = new ArrayCollection();
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

    public function getMois(): ?string
    {
        return $this->mois;
    }

    public function setMois(string $mois): static
    {
        $this->mois = $mois;

        return $this;
    }



    public function getDatedepense(): ?\DateTimeInterface
    {
        return $this->datedepense;
    }

    public function setDatedepense(\DateTimeInterface $datedepense): static
    {
        $this->datedepense = $datedepense;

        return $this;
    }

    /**
     * @return Collection<int, Lignedepense>
     */
    public function getLignedepenses(): Collection
    {
        return $this->lignedepenses;
    }

    public function addLignedepense(Lignedepense $lignedepense): static
    {
        if (!$this->lignedepenses->contains($lignedepense)) {
            $this->lignedepenses->add($lignedepense);
            $lignedepense->setDepenses($this);
        }

        return $this;
    }

    public function removeLignedepense(Lignedepense $lignedepense): static
    {
        if ($this->lignedepenses->removeElement($lignedepense)) {
            // set the owning side to null (unless already changed)
            if ($lignedepense->getDepenses() === $this) {
                $lignedepense->setDepenses(null);
            }
        }

        return $this;
    }
}
