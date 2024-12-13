<?php

namespace App\Entity;

use App\Repository\PieceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PieceRepository::class)]
class Piece
{

 

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;


    #[ORM\ManyToOne(targetEntity: Dossier::class, inversedBy: 'pieces')]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    private $dossier;


    #[ORM\ManyToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: false)]
    private ?FichierAdmin $path = null;

    

    #[ORM\Column(length: 255)]
    private ?string $attribut = null;



    #[ORM\ManyToOne(inversedBy: 'allPieces')]
    private ?Client $client = null;

    #[ORM\ManyToOne(inversedBy: 'pieces')]
    private ?DocumentTypeClient $type = null;



    public function __construct()
    {
    
    }

    
    public function getPath(): ?FichierAdmin
    {
        return $this->path;
    }

    public function setPath(?FichierAdmin $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getDossier(): ?Dossier
    {
        return $this->dossier;
    }

    public function setDossier(?Dossier $dossier): self
    {
        $this->dossier = $dossier;

        return $this;
    }

    public function getAttribut(): ?string
    {
        return $this->attribut;
    }

    public function setAttribut(string $attribut): static
    {
        $this->attribut = $attribut;

        return $this;
    }




    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getType(): ?DocumentTypeClient
    {
        return $this->type;
    }

    public function setType(?DocumentTypeClient $type): static
    {
        $this->type = $type;

        return $this;
    }


}
