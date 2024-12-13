<?php

namespace App\Entity;

use App\Repository\MarcheRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Gedmo\Mapping\Annotation as Gedmo; 

#[ORM\Entity(repositoryClass: MarcheRepository::class)]
class Marche
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(length: 255)]
    private ?string $montanttotal = null;

    #[ORM\Column(length: 255)]
    #[ORM\JoinColumn(nullable: false)]
    private ?string $solde = null;




    #[ORM\ManyToOne(inversedBy: 'marches')]
    private ?Fournisseur $fournisseur = null;

    #[ORM\OneToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    private ?FichierAdmin $path = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Gedmo\Timestampable(on: 'create')]
    private ?\DateTimeInterface $datecreation = null;


<<<<<<< HEAD
    #[ORM\OneToMany(mappedBy: 'marches', targetEntity: Comptefour::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
=======
    #[ORM\OneToMany(mappedBy: 'marches', targetEntity: CompteFournisseur::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
>>>>>>> b6f1842a7fec2506df675de17037826c2c1327b4
    private Collection $compteFournisseurs;
    public function __construct()
    {
        $this->compteFournisseurs = new ArrayCollection();
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

    public function getMontanttotal(): ?string
    {
        return $this->montanttotal;
    }

    public function setMontanttotal(string $montanttotal): static
    {
        $this->montanttotal = $montanttotal;

        return $this;
    }

    public function getPath(): ?FichierAdmin
    {
        return $this->path;
    }

    public function setPath(?FichierAdmin $path): self
    {
        $this->path = $path;

        return $this;
    }

   


    public function getFournisseur(): ?Fournisseur
    {
        return $this->fournisseur;
    }

    public function setFournisseur(?Fournisseur $fournisseur): static
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    public function getDatecreation(): ?\DateTimeInterface
    {
        return $this->datecreation;
    }

    public function setDatecreation(?\DateTimeInterface $datecreation): static
    {
        $this->datecreation = $datecreation;

        return $this;
    }


    public function getSolde(): ?string
    {
        return $this->solde;
    }

    public function setSolde(string $solde): static
    {
        $this->solde = $solde;

        return $this;
    }

   
    /**
<<<<<<< HEAD
     * @return Collection<int, Comptefour>
     */
    public function getComptefours(): Collection
=======
     * @return Collection<int, CompteFournisseur>
     */
    public function getCompteFournisseurs(): Collection
>>>>>>> b6f1842a7fec2506df675de17037826c2c1327b4
    {
        return $this->compteFournisseurs;
    }

<<<<<<< HEAD
    public function addComptefour(Comptefour $compteFournisseur): static
=======
    public function addCompteFournisseur(CompteFournisseur $compteFournisseur): static
>>>>>>> b6f1842a7fec2506df675de17037826c2c1327b4
    {
        if (!$this->compteFournisseurs->contains($compteFournisseur)) {
            $this->compteFournisseurs->add($compteFournisseur);
            $compteFournisseur->setMarches($this);
        }

        return $this;
    }

<<<<<<< HEAD
    public function removeComptefour(Comptefour $compteFournisseur): static
=======
    public function removeCompteFournisseur(CompteFournisseur $compteFournisseur): static
>>>>>>> b6f1842a7fec2506df675de17037826c2c1327b4
    {
        if ($this->compteFournisseurs->removeElement($compteFournisseur)) {
            // set the owning side to null (unless already changed)
            if ($compteFournisseur->getMarches() === $this) {
                $compteFournisseur->setMarches(null);
            }
        }

        return $this;
    }
}
