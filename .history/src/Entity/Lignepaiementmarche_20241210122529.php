<?php

namespace App\Entity;

use App\Repository\LignepaiementmarcheRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LignepaiementmarcheRepository::class)]
class Lignepaiementmarche
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $datepaiement = null;

    #[ORM\Column(length: 255)]
    private ?string $montantpaye = null;
    #[ORM\OneToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    private ?FichierAdmin $path = null;

 
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatepaiement(): ?\DateTimeInterface
    {
        return $this->datepaiement;
    }

    public function setDatepaiement(?\DateTimeInterface $datepaiement): static
    {
        $this->datepaiement = $datepaiement;

        return $this;
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
}
