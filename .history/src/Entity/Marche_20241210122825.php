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

    #[ORM\Column(type: 'boolean')]
    #[ORM\JoinColumn(nullable: false)]
    private $active;

    #[ORM\Column(  type: Types::JSON)]
    #[ORM\JoinColumn(nullable: false)]
    private $etat = [];

    #[ORM\OneToMany(mappedBy: 'marche', targetEntity: Paimentmarche::class)]
    private Collection $paimentmarches;

    #[ORM\ManyToOne(inversedBy: 'marches')]
    private ?Fournisseur $fournisseur = null;

    #[ORM\OneToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    private ?FichierAdmin $path = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Gedmo\Timestampable(on: 'create')]
    private ?\DateTimeInterface $datecreation = null;

    #[ORM\OneToMany(mappedBy: 'marches', targetEntity: Lignepaiementmarche::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Collection $lignepaiementmarches;
    public function __construct()
    {
        $this->paimentmarches = new ArrayCollection();
        $this->lignepaiementmarches = new ArrayCollection();
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

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

   

    /**
     * @return Collection<int, Paimentmarche>
     */
    public function getPaimentmarches(): Collection
    {
        return $this->paimentmarches;
    }

    public function addPaimentmarch(Paimentmarche $paimentmarch): static
    {
        if (!$this->paimentmarches->contains($paimentmarch)) {
            $this->paimentmarches->add($paimentmarch);
            $paimentmarch->setMarche($this);
        }

        return $this;
    }

    public function removePaimentmarch(Paimentmarche $paimentmarch): static
    {
        if ($this->paimentmarches->removeElement($paimentmarch)) {
            // set the owning side to null (unless already changed)
            if ($paimentmarch->getMarche() === $this) {
                $paimentmarch->setMarche(null);
            }
        }

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

    public function getEtat(): ?array
    {
        return $this->etat;
    }

    public function setEtat(array $etat): self
    {
        $this->etat = $etat;

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
     * @return Collection<int, Lignepaiementmarche>
     */
    public function getLignepaiementmarches(): Collection
    {
        return $this->lignepaiementmarches;
    }

    public function addLignepaiementmarch(Lignepaiementmarche $lignepaiementmarch): static
    {
        if (!$this->lignepaiementmarches->contains($lignepaiementmarch)) {
            $this->lignepaiementmarches->add($lignepaiementmarch);
            $lignepaiementmarch->setMarches($this);
        }

        return $this;
    }

    public function removeLignepaiementmarch(Lignepaiementmarche $lignepaiementmarch): static
    {
        if ($this->lignepaiementmarches->removeElement($lignepaiementmarch)) {
            // set the owning side to null (unless already changed)
            if ($lignepaiementmarch->getMarches() === $this) {
                $lignepaiementmarch->setMarches(null);
            }
        }

        return $this;
    }
}
