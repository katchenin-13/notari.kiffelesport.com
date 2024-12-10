<?php

namespace App\Entity;

use App\Repository\DocumentClientRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentClientRepository::class)]
class DocumentClient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;


    #[ORM\ManyToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    private ?FichierAdmin $fichier = null;



    #[ORM\ManyToOne(inversedBy: 'documentClients')]
    private ?Client $client = null;

    #[ORM\ManyToOne(inversedBy: 'documentClients')]
    private ?DocumentTypeClient $document_type_client = null;
    

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

    

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getDocumentTypeClient(): ?DocumentTypeClient
    {
        return $this->document_type_client;
    }

    public function setDocumentTypeClient(?DocumentTypeClient $document_type_client): static
    {
        $this->document_type_client = $document_type_client;

        return $this;
    }


}
