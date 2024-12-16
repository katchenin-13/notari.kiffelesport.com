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

    #[ORM\OneToMany(mappedBy: 'type', targetEntity: Piece::class)]
    private Collection $pieces;

    #[ORM\ManyToOne(inversedBy: 'documentTypeClients')]
    private ?Typedocument $typesdocuments = null;

 

    public function __construct()
    {
        $this->documentClients = new ArrayCollection();
        $this->pieces = new ArrayCollection();
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

    /**
     * @return Collection<int, Piece>
     */
    public function getPieces(): Collection
    {
        return $this->pieces;
    }

    public function addPiece(Piece $piece): static
    {
        if (!$this->pieces->contains($piece)) {
            $this->pieces->add($piece);
            $piece->setType($this);
        }

        return $this;
    }

    public function removePiece(Piece $piece): static
    {
        if ($this->pieces->removeElement($piece)) {
            // set the owning side to null (unless already changed)
            if ($piece->getType() === $this) {
                $piece->setType(null);
            }
        }

        return $this;
    }

    public function getTypesdocuments(): ?Typedocument
    {
        return $this->typesdocuments;
    }

    public function setTypesdocuments(?Typedocument $typesdocuments): static
    {
        $this->typesdocuments = $typesdocuments;

        return $this;
    }

   

    
}
