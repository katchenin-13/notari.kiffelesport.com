<?php

namespace App\Entity;

use App\Form\TypeClientType;
use App\Repository\DocumentTypeClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentTypeClientRepository::class)]
class DocumentTypeClient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\ManyToOne(inversedBy: 'documentTypeClients')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeClient $typeclient = null;

    #[ORM\OneToMany(mappedBy: 'document_type_client', targetEntity: DocumentClient::class)]
    private Collection $documentClients;

 

    public function __construct()
    {
        $this->documentClients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getTypeclient(): ?TypeClient
    {
        return $this->typeclient;
    }

    public function setTypeclient(?TypeClient $typeclient): static
    {
        $this->typeclient = $typeclient;

        return $this;
    }

    /**
     * @return Collection<int, DocumentClient>
     */
    public function getDocumentClients(): Collection
    {
        return $this->documentClients;
    }

    public function addDocumentClient(DocumentClient $documentClient): static
    {
        if (!$this->documentClients->contains($documentClient)) {
            $this->documentClients->add($documentClient);
            $documentClient->setDocumentTypeClient($this);
        }

        return $this;
    }

    public function removeDocumentClient(DocumentClient $documentClient): static
    {
        if ($this->documentClients->removeElement($documentClient)) {
            // set the owning side to null (unless already changed)
            if ($documentClient->getDocumentTypeClient() === $this) {
                $documentClient->setDocumentTypeClient(null);
            }
        }

        return $this;
    }

   

    
}
