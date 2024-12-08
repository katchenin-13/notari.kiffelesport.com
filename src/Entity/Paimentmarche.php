<?php

namespace App\Entity;

use App\Repository\PaimentmarcheRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaimentmarcheRepository::class)]
class Paimentmarche
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $montantpaye = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $datepaiement = null;

    #[ORM\ManyToOne(inversedBy: 'paimentmarches')]
    private ?Marche $marche = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontantpaye(): ?string
    {
        return $this->montantpaye;
    }

    public function setMontantpaye(string $montantpaye): static
    {
        $this->montantpaye = $montantpaye;

        return $this;
    }

    public function getDatepaiement(): ?\DateTimeInterface
    {
        return $this->datepaiement;
    }

    public function setDatepaiement(\DateTimeInterface $datepaiement): static
    {
        $this->datepaiement = $datepaiement;

        return $this;
    }

    public function getMarche(): ?Marche
    {
        return $this->marche;
    }

    public function setMarche(?Marche $marche): static
    {
        $this->marche = $marche;

        return $this;
    }
}
