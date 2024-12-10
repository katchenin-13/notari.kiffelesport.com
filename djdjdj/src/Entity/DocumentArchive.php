<?php

namespace App\Entity;

use App\Repository\DocumentArchiveRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentArchiveRepository::class)]
class DocumentArchive
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: DocumentTypeActe::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $document;

    #[ORM\ManyToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    private ?FichierAdmin $fichier = null;

    #[ORM\ManyToOne(targetEntity: Archive::class, inversedBy: 'documents')]
    #[ORM\JoinColumn(nullable: false)]
    private $archive;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDocument(): ?DocumentTypeActe
    {
        return $this->document;
    }

    public function setDocument(?DocumentTypeActe $document): self
    {
        $this->document = $document;

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

    public function getArchive(): ?Archive
    {
        return $this->archive;
    }

    public function setArchive(?Archive $archive): self
    {
        $this->archive = $archive;

        return $this;
    }
}
