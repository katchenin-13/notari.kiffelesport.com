<?php

namespace App\Entity;

use App\Repository\TypedocumentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypedocumentRepository::class)]
class Typedocument
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: 'typesdocuments', targetEntity: DocumentTypeClient::class)]
    private Collection $documentTypeClients;

    public function __construct()
    {
        $this->documentTypeClients = new ArrayCollection();
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

    /**
     * @return Collection<int, DocumentTypeClient>
     */
    public function getDocumentTypeClients(): Collection
    {
       
    }

    public function addDocumentTypeClient(DocumentTypeClient $documentTypeClient): static
    {
        if (!$this->documentTypeClients->contains($documentTypeClient)) {
            $this->documentTypeClients->add($documentTypeClient);
            $documentTypeClient->setTypesdocuments($this);
        }

        return $this;
    }

    public function removeDocumentTypeClient(DocumentTypeClient $documentTypeClient): static
    {
        if ($this->documentTypeClients->removeElement($documentTypeClient)) {
            // set the owning side to null (unless already changed)
            if ($documentTypeClient->getTypesdocuments() === $this) {
                $documentTypeClient->setTypesdocuments(null);
            }
        }

        return $this;
    }
}
