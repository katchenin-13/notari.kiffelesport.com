<?php

namespace App\Entity;

use App\Repository\TypeClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeClientRepository::class)]
class TypeClient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $titre;

    #[ORM\OneToMany(targetEntity: Client::class, mappedBy: 'type_client')]
    private $clients;

    #[ORM\Column(type: 'string', length: 255)]
    private $active;

    #[ORM\Column(type: 'string', length: 10)]
    private $code;

    #[ORM\OneToMany(mappedBy: 'typeclient', targetEntity: DocumentTypeClient::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $documentTypeClients;

    #[ORM\OneToMany(mappedBy: 'type', targetEntity: Identification::class)]
    private Collection $identifications;




    public function __construct()
    {
        $this->clients = new ArrayCollection();
        $this->active = 1;
        $this->documentTypeClients = new ArrayCollection();
        $this->identifications = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Client>
     */
    public function getClients(): Collection
    {
        return $this->clients;
    }

    public function addClient(Client $client): self
    {
        if (!$this->clients->contains($client)) {
            $this->clients[] = $client;
            $client->setTypeClient($this);
        }

        return $this;
    }

    public function removeClient(Client $client): self
    {
        if ($this->clients->removeElement($client)) {
            // set the owning side to null (unless already changed)
            if ($client->getTypeClient() === $this) {
                $client->setTypeClient(null);
            }
        }

        return $this;
    }

    public function getActive(): ?string
    {
        return $this->active;
    }

    public function setActive(string $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Collection<int, DocumentTypeClient>
     */
    public function getDocumentTypeClients(): Collection
    {
        return $this->documentTypeClients;
    }

    public function addDocumentTypeClient(DocumentTypeClient $documentTypeClient): static
    {
        if (!$this->documentTypeClients->contains($documentTypeClient)) {
            $this->documentTypeClients->add($documentTypeClient);
            $documentTypeClient->setTypeclient($this);
        }

        return $this;
    }

    public function removeDocumentTypeClient(DocumentTypeClient $documentTypeClient): static
    {
        if ($this->documentTypeClients->removeElement($documentTypeClient)) {
            // set the owning side to null (unless already changed)
            if ($documentTypeClient->getTypeclient() === $this) {
                $documentTypeClient->setTypeclient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Identification>
     */
    public function getIdentifications(): Collection
    {
        return $this->identifications;
    }

    public function addIdentification(Identification $identification): static
    {
        if (!$this->identifications->contains($identification)) {
            $this->identifications->add($identification);
            $identification->setType($this);
        }

        return $this;
    }

    public function removeIdentification(Identification $identification): static
    {
        if ($this->identifications->removeElement($identification)) {
            // set the owning side to null (unless already changed)
            if ($identification->getType() === $this) {
                $identification->setType(null);
            }
        }

        return $this;
    }

    
   
}
