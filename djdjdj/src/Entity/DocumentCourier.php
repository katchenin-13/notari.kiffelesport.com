<?php

namespace App\Entity;

use App\Repository\DocumentCourierRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentCourierRepository::class)]
class DocumentCourier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $titre;

    #[ORM\ManyToOne(targetEntity: CourierArrive::class, inversedBy: 'documentCouriers')]
    private $courier;


    #[ORM\ManyToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    private ?FichierAdmin $fichier = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getCourier(): ?CourierArrive
    {
        return $this->courier;
    }

    public function setCourier(?CourierArrive $courier): self
    {
        $this->courier = $courier;

        return $this;
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
}
