<?php

namespace App\Entity;

use App\Repository\ImputationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImputationRepository::class)]
class Imputation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'imputations')]
    private ?Employe $employe = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $commentaire = null;

    #[ORM\ManyToOne(inversedBy: 'imputations')]
    private ?CourierArrive $courierArrive = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmploye(): ?Employe
    {
        return $this->employe;
    }

    public function setEmploye(?Employe $employe): static
    {
        $this->employe = $employe;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): static
    {
        $this->commentaire = $commentaire;

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
