<?php

namespace App\Entity;

use App\Repository\RemiseActeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RemiseActeRepository::class)]
class RemiseActe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $date;


    #[ORM\OneToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    private ?FichierAdmin $expedition = null;

    #[ORM\OneToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    private ?FichierAdmin $copie = null;

    #[ORM\OneToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    private ?FichierAdmin $grosse = null;

    #[ORM\ManyToOne(targetEntity: Dossier::class, inversedBy: 'remiseActes')]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    private $dossier;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $commentaire = null;

    #[ORM\Column(type: 'boolean')]
    private $active;

    public function __construct()
    {
        $this->active = false;
    }

    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
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


    public function getExpedition(): ?FichierAdmin
    {
        return $this->expedition;
    }

    public function setExpedition(?FichierAdmin $expedition): self
    {
        /*  if ($fichier->getFile()) { */
        $this->expedition = $expedition;
        /*  } */
        return $this;
    }


    public function getCopie(): ?FichierAdmin
    {
        return $this->copie;
    }

    public function setCopie(?FichierAdmin $copie): self
    {
        /*  if ($fichier->getFile()) { */
        $this->copie = $copie;
        /*  } */

        return $this;
    }


    public function getGrosse(): ?FichierAdmin
    {
        return $this->grosse;
    }

    public function setGrosse(?FichierAdmin $grosse): self
    {
        /*  if ($fichier->getFile()) { */
        $this->grosse = $grosse;
        /*  } */

        return $this;
    }
 

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

}
