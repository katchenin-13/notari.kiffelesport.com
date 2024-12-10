<?php

namespace App\Entity;

use App\Repository\DocumentTypeActeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentTypeActeRepository::class)]
class DocumentTypeActe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;



    #[ORM\Column(type: 'json')]
    private $etapes = [];

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\ManyToOne(inversedBy: 'documentTypeActes')]
    private ?Type $type = null;

    #[ORM\OneToMany(mappedBy: 'document', targetEntity: DocumentClient::class)]
    private Collection $documentClients;

    public function __construct()
    {
        $this->documentClients = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }




    public function getEtapes(): ?array
    {
        return $this->etapes;
    }

    public function setEtapes(array $etapes): self
    {
        $this->etapes = $etapes;

        return $this;
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

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): static
    {
        $this->type = $type;

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
            $documentClient->setDocument($this);
        }

        return $this;
    }

    public function removeDocumentClient(DocumentClient $documentClient): static
    {
        if ($this->documentClients->removeElement($documentClient)) {
            // set the owning side to null (unless already changed)
            if ($documentClient->getDocument() === $this) {
                $documentClient->setDocument(null);
            }
        }

        return $this;
    }
}
