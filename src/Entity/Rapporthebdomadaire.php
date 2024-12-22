<?php

namespace App\Entity;

use App\Repository\RapporthebdomadaireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo; 

#[ORM\Entity(repositoryClass: RapporthebdomadaireRepository::class)]
class Rapporthebdomadaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;




    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\OneToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: false)]
    private ?FichierAdmin $fichier = null;


    #[ORM\ManyToOne(inversedBy: 'rapporthebdomadaires')]
    #[ORM\JoinColumn(nullable: false)]
    #[Gedmo\Blameable(on: 'create')]
    private ?utilisateur $utilisateur = null;



    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $daterapports = null;



  
 
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

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

    public function getUtilisateur(): ?utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }


    // public function getEmploye(): ?employe
    // {
    //     return $this->employe;
    // }

    // public function setEmploye(?employe $employe): self
    // {
    //     $this->employe = $employe;

    //     return $this;
    // }

    public function getDaterapports(): ?\DateTimeInterface
    {
        return $this->daterapports;
    }

    public function setDaterapports(\DateTimeInterface $daterapports): static
    {
        $this->daterapports = $daterapports;

        return $this;
    }

  
}
