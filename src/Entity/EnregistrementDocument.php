<?php

namespace App\Entity;

use App\Repository\EnregistrementDocumentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EnregistrementDocumentRepository::class)]
class EnregistrementDocument
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'enregistrementDocuments')]
    private ?Dossier $dossier = null;

    
    #[ORM\ManyToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    private ?FichierAdmin $fichier = null;

    #[ORM\ManyToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    private ?FichierAdmin $fichierClient = null;

    #[ORM\ManyToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    private ?FichierAdmin $fichierCourrier = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDossier(): ?Dossier
    {
        return $this->dossier;
    }

    public function setDossier(?Dossier $dossier): static
    {
        $this->dossier = $dossier;

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

    public function setFichierClient(?FichierAdmin $fichier): self
    {

        $this->fichierClient = $fichier;
        return $this;
    }
    public function getFichierClient(): ?FichierAdmin
    {
        return $this->fichierClient;
    }

    public function setFichierCourrier(?FichierAdmin $fichier): self
    {

        $this->fichierCourrier = $fichier;
        return $this;
    }
    public function getFichierCourrier(): ?FichierAdmin
    {
        return $this->fichierCourrier;
    }

}
