<?php

namespace App\Entity;

use App\Repository\TypedepenseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypedepenseRepository::class)]
class Typedepense
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: 'typedepense', targetEntity: Lignedepense::class, orphanRemoval: true, cascade: ["persist", "remove"])]
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
            $lignedepense->setTypedepense($this);
        }

        return $this;
    }

    public function removeLignedepense(Lignedepense $lignedepense): static
    {
        if ($this->lignedepenses->removeElement($lignedepense)) {
            // set the owning side to null (unless already changed)
            if ($lignedepense->getTypedepense() === $this) {
                $lignedepense->setTypedepense(null);
            }
        }

        return $this;
    }
}
