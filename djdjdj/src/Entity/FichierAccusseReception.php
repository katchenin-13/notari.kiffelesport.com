<?php

namespace App\Entity;

use App\Repository\FichierAccusseReceptionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FichierAccusseReceptionRepository::class)]
class FichierAccusseReception
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    private ?FichierAdmin $fichier = null;

    #[ORM\ManyToOne(inversedBy: 'fichierAccusseReceptions')]
    private ?CourierArrive $courierArrive = null;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getFichier(): ?FichierAdmin
    {
        return $this->fichier;
    }

    public function setFichier(?FichierAdmin $fichier): self
    {
        $this->fichier = $fichier;

        return $this;
    }
    public function getCourierArrive(): ?CourierArrive
    {
        return $this->courierArrive;
    }

    public function setCourierArrive(?CourierArrive $courierArrive): static
    {
        $this->courierArrive = $courierArrive;

        return $this;
    }
}
