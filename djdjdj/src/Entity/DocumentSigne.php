<?php

namespace App\Entity;

use App\Repository\DocumentSigneRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentSigneRepository::class)]
class DocumentSigne
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;


    #[ORM\ManyToOne(targetEntity: Dossier::class, inversedBy: 'documentSignes')]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    private $dossier;

    #[ORM\Column(type: Types::DATETIME_MUTABLE,nullable:true)]
    private ?\DateTimeInterface $dateSignature = null;

    #[ORM\ManyToOne(inversedBy: 'documentSignes')]
    private ?Client $client = null;



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

    public function getDateSignature(): ?\DateTimeInterface
    {
        return $this->dateSignature;
    }

    public function setDateSignature(\DateTimeInterface $dateSignature): static
    {
        $this->dateSignature = $dateSignature;

        return $this;
    }

    public function getClient(): ?CLient
    {
        return $this->client;
    }

    public function setClient(?CLient $client): static
    {
        $this->client = $client;

        return $this;
    }

    
}
