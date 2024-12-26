<?php

namespace App\Entity;

use App\Repository\DossierRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Gedmo\Mapping\Annotation as Gedmo; // gedmo annotations
use Monolog\Handler\Curl\Util;

#[ORM\Entity(repositoryClass: DossierRepository::class)]
#[UniqueEntity(fields: ['numeroRepertoire'], message: 'Ce numero de répertoire existe déja')]
#[UniqueEntity(fields: ['numeroOuverture'], message: "Ce numero d'ouverture existe déja")]
class Dossier
{


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private $numeroOuverture;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $montantVendeur;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $montantAcheteur;

    #[ORM\Column(type: 'datetime')]
    private $dateCreation;



    #[ORM\Column(type: 'boolean')]
    private $active;


    #[ORM\Column(type: 'json', nullable: true)]
    private $etat = [];

    #[ORM\Column(type: 'string', length: 255)]
    private $objet;

    #[ORM\ManyToOne(targetEntity: Type::class, inversedBy: 'dossiers')]
    // #[ORM\JoinColumn(nullable: false)]
    private $typeActe;

    #[ORM\OneToMany(targetEntity: DossierWorkflow::class, mappedBy: 'dossier', cascade: ['persist'])]
    private $dossierWorkflows;

    #[ORM\OneToMany(targetEntity: Identification::class, mappedBy: 'dossier', cascade: ['persist'])]
    #[Assert\Valid(groups: ['identification'])]
    private $identifications;



    #[ORM\OneToMany(targetEntity: Piece::class, mappedBy: 'dossier', cascade: ['persist'])]
    private $pieces;

    #[ORM\OneToMany(targetEntity: DocumentSigne::class, mappedBy: 'dossier', cascade: ['persist'])]
    private $documentSignes;

    #[ORM\Column(type: 'string', length: 255)]
    private $etape;

    #[ORM\Column(type: 'text')]
    private $description;

    #[ORM\OneToMany(targetEntity: Enregistrement::class, mappedBy: 'dossier', cascade: ['persist'])]
    private $enregistrements;

    #[ORM\OneToMany(targetEntity: PieceVendeur::class, mappedBy: 'dossier', cascade: ['persist'])]
    private $pieceVendeurs;

    #[ORM\OneToMany(targetEntity: Redaction::class, mappedBy: 'dossier', cascade: ['persist'])]
    private $redactions;

    #[ORM\OneToMany(targetEntity: Obtention::class, mappedBy: 'dossier', cascade: ['persist'])]
    private $obtentions;

    #[ORM\OneToMany(targetEntity: Remise::class, mappedBy: 'dossier', cascade: ['persist'])]
    private $remises;

    #[ORM\OneToMany(targetEntity: RemiseActe::class, mappedBy: 'dossier', cascade: ['persist', 'remove'])]
    private $remiseActes;

    #[ORM\OneToOne(targetEntity: InfoClassification::class, mappedBy: 'dossier', cascade: ['persist', 'remove'])]
    private $infoClassification;

    #[ORM\Column(type: 'string', length: 255)]
    private $montantTotal;

    #[ORM\ManyToOne(targetEntity: Conservation::class, inversedBy: 'dossiers', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private $conservation;

    // #[ORM\OneToMany(targetEntity: PaiementFrais::class, mappedBy: 'dossier', cascade: ['persist'])]
    // private $paiementFrais;

    #[ORM\Column(type: 'string', length: 255, nullable: true, unique: true)]
    private $numeroRepertoire;

    #[ORM\ManyToOne(inversedBy: 'dossiers')]
    private ?Entreprise $entreprise = null;

    #[ORM\ManyToOne(inversedBy: 'dossiers')]
    private ?employe $employe = null;
    

    // #[ORM\ManyToOne(inversedBy: 'dossiers')]
    // #[ORM\JoinColumn(nullable: false)]
    // #[Gedmo\Blameable(on: 'create')]
    // private ?utilisateur $utilisateur = null;

    #[ORM\OneToMany(targetEntity: Calendar::class, mappedBy: 'dossier',  cascade: ['persist'] )]
    private $calendars;

    #[ORM\ManyToOne(inversedBy: 'dossiers')]
    #[ORM\JoinColumn(nullable: false)]
    #[Gedmo\Blameable(on: 'create')]
    private ?Utilisateur $utilisateur = null;

    // #[ORM\OneToMany(mappedBy: 'dossier', targetEntity: Compte::class)]
    // private Collection $comptes;

    #[ORM\OneToMany(mappedBy: 'dossier', targetEntity: DocumentSigneFichier::class, cascade: ['persist'])]
    private Collection $documentSigneFichiers;

    // #[ORM\OneToMany(mappedBy: 'dossier', targetEntity: CommentaireIdentification::class)]
    // private Collection $CommentaireIdentifications;

    #[ORM\OneToMany(mappedBy: 'dossier', targetEntity: CommentairePiece::class, cascade: ['persist'])]
    private Collection $commentairePieces;

    // #[ORM\OneToMany(mappedBy: 'dossier', targetEntity: CommentairePaiement::class)]
    // private Collection $commentairePaiements;

    #[ORM\OneToMany(mappedBy: 'dossier', targetEntity: CommentaireRedaction::class, cascade: ['persist'])]
    private Collection $commentaireRedactions;

    #[ORM\OneToMany(mappedBy: 'dossier', targetEntity: CommentaireSignature::class, cascade: ['persist'])]
    private Collection $commentaireSignatures;

    #[ORM\OneToMany(mappedBy: 'dossier', targetEntity: CommentaireEng::class, cascade: ['persist'])]
    private Collection $commentaireEngs;

    #[ORM\OneToMany(mappedBy: 'dossier', targetEntity: CommentaireObtention::class, cascade: ['persist'])]
    private Collection $commentaireObtentions;

    #[ORM\OneToMany(mappedBy: 'dossier', targetEntity: EnregistrementDocument::class, cascade: ['persist'])]
    private Collection $enregistrementDocuments;

    #[ORM\Column(length: 50)]
    #[ORM\JoinColumn(nullable: true)]
    #[Assert\Choice(choices: ['societe', 'notariat'], message: 'Veuillez sélectionner une option valide.')]
    private ?string $natureDossier;

    #[ORM\Column(length: 255)]
    #[ORM\JoinColumn(nullable: false)]
    private ?string $numcompte=null;

    public function __construct()
    {
        $this->calendars = new ArrayCollection();
        $this->dossierWorkflows = new ArrayCollection();
        $this->identifications = new ArrayCollection();
        $this->pieces = new ArrayCollection();
        $this->documentSignes = new ArrayCollection();
        $this->enregistrements = new ArrayCollection();
        $this->pieceVendeurs = new ArrayCollection();
        $this->redactions = new ArrayCollection();
        $this->obtentions = new ArrayCollection();
        $this->remises = new ArrayCollection();
        $this->remiseActes = new ArrayCollection();
        $this->setActive(true);
        $this->setMontantAcheteur(0);
        $this->setMontantVendeur(0);
        //$this->paiementFrais = new ArrayCollection();
        $this->dateCreation = new DateTime();
    //    $this->comptes = new ArrayCollection();
        $this->documentSigneFichiers = new ArrayCollection();
        // $this->CommentaireIdentifications = new ArrayCollection();
        $this->commentairePieces = new ArrayCollection();
        // $this->commentairePaiements = new ArrayCollection();
        $this->commentaireRedactions = new ArrayCollection();
        $this->commentaireSignatures = new ArrayCollection();
        $this->commentaireEngs = new ArrayCollection();
        $this->commentaireObtentions = new ArrayCollection();
        $this->enregistrementDocuments = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getMontantVendeur(): ?string
    {
        return $this->montantVendeur;
    }

    public function setMontantVendeur(string $montantVendeur): self
    {
        $this->montantVendeur = $montantVendeur;

        return $this;
    }

    public function getMontantAcheteur(): ?string
    {
        return $this->montantAcheteur;
    }

    public function setMontantAcheteur(string $montantAcheteur): self
    {
        $this->montantAcheteur = $montantAcheteur;

        return $this;
    }

    public function getNumeroOuverture(): ?string
    {
        return $this->numeroOuverture;
    }

    public function setNumeroOuverture(string $numeroOuverture): self
    {
        $this->numeroOuverture = $numeroOuverture;

        return $this;
    }

    /*  public function getNumeroClassification(): ?string
    {
        return $this->numeroClassification;
    }

    public function setNumeroClassification(string $numeroClassification): self
    {
        $this->numeroClassification = $numeroClassification;

        return $this;
    } */

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /*   public function getDateClassification(): ?\DateTimeInterface
    {
        return $this->dateClassification;
    }

    public function setDateClassification(\DateTimeInterface $dateClassification): self
    {
        $this->dateClassification = $dateClassification;

        return $this;
    } */



    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

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

    public function getObjet(): ?string
    {
        return $this->objet;
    }

    public function setObjet(string $objet): self
    {
        $this->objet = $objet;

        return $this;
    }

    public function getTypeActe(): ?Type
    {
        return $this->typeActe;
    }

    public function setTypeActe(?Type $typeActe): self
    {
        $this->typeActe = $typeActe;

        return $this;
    }

    /**
     * @return Collection<int, DossierWorkflow>
     */
    public function getDossierWorkflows(): Collection
    {
        return $this->dossierWorkflows;
    }

    public function addDossierWorkflow(DossierWorkflow $dossierWorkflow): self
    {
        if (!$this->dossierWorkflows->contains($dossierWorkflow)) {
            $this->dossierWorkflows[] = $dossierWorkflow;
            $dossierWorkflow->setDossier($this);
        }

        return $this;
    }

    public function removeDossierWorkflow(DossierWorkflow $dossierWorkflow): self
    {
        if ($this->dossierWorkflows->removeElement($dossierWorkflow)) {
            // set the owning side to null (unless already changed)
            if ($dossierWorkflow->getDossier() === $this) {
                $dossierWorkflow->setDossier(null);
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

    public function addIdentification(Identification $identification): self
    {
        if (!$this->identifications->contains($identification)) {
            $this->identifications[] = $identification;
            $identification->setDossier($this);
        }

        return $this;
    }

    public function removeIdentification(Identification $identification): self
    {
        if ($this->identifications->removeElement($identification)) {
            // set the owning side to null (unless already changed)
            if ($identification->getDossier() === $this) {
                $identification->setDossier(null);
            }
        }

        return $this;
    }

    //  /**
    //  * @return Collection<int, Compte>
    //  */
    // public function getComptes(): Collection
    // {
    //     return $this->comptes;
    // }

    // public function addCompte(Compte $compte): static
    // {
    //     if (!$this->comptes->contains($compte)) {
    //         $this->comptes->add($compte);
    //         $compte->setDossier($this);
    //     }

    //     return $this;
    // }

    // public function removeCompte(Compte $compte): static
    // {
    //     if ($this->comptes->removeElement($compte)) {
    //         // set the owning side to null (unless already changed)
    //         if ($compte->getDossier() === $this) {
    //             $compte->setDossier(null);
    //         }
    //     }

    //     return $this;
    // }


    /**
     * @return Collection<int, Piece>
     */
    public function getPieces(): Collection
    {
        return $this->pieces;
    }

    public function addPiece(Piece $piece): self
    {
        if (!$this->pieces->contains($piece)) {
            $this->pieces[] = $piece;
            $piece->setDossier($this);
        }

        return $this;
    }

    public function removePiece(Piece $piece): self
    {
        if ($this->pieces->removeElement($piece)) {
            // set the owning side to null (unless already changed)
            if ($piece->getDossier() === $this) {
                $piece->setDossier(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DocumentSigne>
     */
    public function getDocumentSignes(): Collection
    {
        return $this->documentSignes;
    }

    public function addDocumentSigne(DocumentSigne $documentSigne): self
    {
        if (!$this->documentSignes->contains($documentSigne)) {
            $this->documentSignes[] = $documentSigne;
            $documentSigne->setDossier($this);
        }

        return $this;
    }

    public function removeDocumentSigne(DocumentSigne $documentSigne): self
    {
        if ($this->documentSignes->removeElement($documentSigne)) {
            // set the owning side to null (unless already changed)
            if ($documentSigne->getDossier() === $this) {
                $documentSigne->setDossier(null);
            }
        }

        return $this;
    }

    public function getEtape(): ?string
    {
        return $this->etape;
    }

    public function setEtape(string $etape): self
    {
        $this->etape = $etape;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Enregistrement>
     */
    public function getEnregistrements(): Collection
    {
        return $this->enregistrements;
    }

    public function addEnregistrement(Enregistrement $enregistrement): self
    {
        if (!$this->enregistrements->contains($enregistrement)) {
            $this->enregistrements[] = $enregistrement;
            $enregistrement->setDossier($this);
        }

        return $this;
    }

    public function removeEnregistrement(Enregistrement $enregistrement): self
    {
        if ($this->enregistrements->removeElement($enregistrement)) {
            // set the owning side to null (unless already changed)
            if ($enregistrement->getDossier() === $this) {
                $enregistrement->setDossier(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PieceVendeur>
     */
    public function getPieceVendeurs(): Collection
    {
        return $this->pieceVendeurs;
    }

    public function addPieceVendeur(PieceVendeur $pieceVendeur): self
    {
        if (!$this->pieceVendeurs->contains($pieceVendeur)) {
            $this->pieceVendeurs[] = $pieceVendeur;
            $pieceVendeur->setDossier($this);
        }

        return $this;
    }

    public function removePieceVendeur(PieceVendeur $pieceVendeur): self
    {
        if ($this->pieceVendeurs->removeElement($pieceVendeur)) {
            // set the owning side to null (unless already changed)
            if ($pieceVendeur->getDossier() === $this) {
                $pieceVendeur->setDossier(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Redaction>
     */
    public function getRedactions(): Collection
    {
        return $this->redactions;
    }

    public function addRedaction(Redaction $redaction): self
    {
        if (!$this->redactions->contains($redaction)) {
            $this->redactions[] = $redaction;
            $redaction->setDossier($this);
        }

        return $this;
    }

    public function removeRedaction(Redaction $redaction): self
    {
        if ($this->redactions->removeElement($redaction)) {
            // set the owning side to null (unless already changed)
            if ($redaction->getDossier() === $this) {
                $redaction->setDossier(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Obtention>
     */
    public function getObtentions(): Collection
    {
        return $this->obtentions;
    }

    public function addObtention(Obtention $obtention): self
    {
        if (!$this->obtentions->contains($obtention)) {
            $this->obtentions[] = $obtention;
            $obtention->setDossier($this);
        }

        return $this;
    }

    public function removeObtention(Obtention $obtention): self
    {
        if ($this->obtentions->removeElement($obtention)) {
            // set the owning side to null (unless already changed)
            if ($obtention->getDossier() === $this) {
                $obtention->setDossier(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Remise>
     */
    public function getRemises(): Collection
    {
        return $this->remises;
    }

    public function addRemise(Remise $remise): self
    {
        if (!$this->remises->contains($remise)) {
            $this->remises[] = $remise;
            $remise->setDossier($this);
        }

        return $this;
    }

    public function removeRemise(Remise $remise): self
    {
        if ($this->remises->removeElement($remise)) {
            // set the owning side to null (unless already changed)
            if ($remise->getDossier() === $this) {
                $remise->setDossier(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RemiseActe>
     */
    public function getRemiseActes(): Collection
    {
        return $this->remiseActes;
    }

    public function addRemiseActe(RemiseActe $remiseActe): self
    {
        if (!$this->remiseActes->contains($remiseActe)) {
            $this->remiseActes[] = $remiseActe;
            $remiseActe->setDossier($this);
        }

        return $this;
    }

    public function removeRemiseActe(RemiseActe $remiseActe): void
    {
        if ($this->remiseActes->removeElement($remiseActe)) {
            // set the owning side to null (unless already changed)
            if ($remiseActe->getDossier() === $this) {
                $remiseActe->setDossier(null);
            }
        }
    }

    public function getInfoClassification(): ?InfoClassification
    {
        return $this->infoClassification;
    }

    public function setInfoClassification(InfoClassification $infoClassification): self
    {
        // set the owning side of the relation if necessary
        if ($infoClassification->getDossier() !== $this) {
            $infoClassification->setDossier($this);
        }

        $this->infoClassification = $infoClassification;

        return $this;
    }


    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context, $payload)
    {
        $enregistrements = $this->getEnregistrements();
/* 
        foreach ($enregistrements as $index => $enregistrement) {

            if ($enregistrement->getDate() && (!$enregistrement->getNumero() ||  !$enregistrement->getFichier())) {
                $context->buildViolation(sprintf(
                    'Veuillez renseigner le numéro et/ou le fichier pour la ligne [%s]',
                    Enregistrement::SENS[$enregistrement->getSens()]
                ))
                    ->addViolation();
            }


            if ($enregistrement->getNumero() && (!$enregistrement->getDate() || !$enregistrement->getFichier())) {
                $context->buildViolation(sprintf(
                    'Veuillez renseigner la date et/ou le fichier pour le numéro [%s] dans la ligne [%s]',
                    $enregistrement->getNumero(),
                    Enregistrement::SENS[$enregistrement->getSens()]
                ))
                    ->addViolation();
            }
        } */
    }

    public function getMontantTotal(): ?string
    {
        return $this->montantTotal;
    }

    public function setMontantTotal(string $montantTotal): self
    {
        $this->montantTotal = $montantTotal;

        return $this;
    }

    public function getConservation(): ?Conservation
    {
        return $this->conservation;
    }

    public function setConservation(?Conservation $conservation): self
    {
        $this->conservation = $conservation;

        return $this;
    }

    // /**
    //  * @return Collection<int, PaiementFrais>
    //  */
    // public function getPaiementFrais(): Collection
    // {
    //     return $this->paiementFrais;
    // }

    // public function addPaiementFrai(PaiementFrais $paiementFrai): self
    // {
    //     if (!$this->paiementFrais->contains($paiementFrai)) {
    //         $this->paiementFrais[] = $paiementFrai;
    //         $paiementFrai->setDossier($this);
    //     }

    //     return $this;
    // }

    // public function removePaiementFrai(PaiementFrais $paiementFrai): self
    // {
    //     if ($this->paiementFrais->removeElement($paiementFrai)) {
    //         // set the owning side to null (unless already changed)
    //         if ($paiementFrai->getDossier() === $this) {
    //             $paiementFrai->setDossier(null);
    //         }
    //     }

    //     return $this;
    // }

    public function getNumeroRepertoire(): ?string
    {
        return $this->numeroRepertoire;
    }

    public function setNumeroRepertoire(?string $numeroRepertoire): self
    {
        $this->numeroRepertoire = $numeroRepertoire;

        return $this;
    }

    public function getEntreprise(): ?Entreprise
    {
        return $this->entreprise;
    }

    public function setEntreprise(?Entreprise $entreprise): self
    {
        $this->entreprise = $entreprise;

        return $this;
    }
    // public function getUtilisateur(): ?utilisateur
    // {
    //     return $this->utilisateur;
    // }

    // public function setUtilisateur(?utilisateur $utilisateur): self
    // {
    //     $this->utilisateur = $utilisateur;

    //     return $this;
    // }
    public function getEmploye(): ?employe
    {
        return $this->employe;
    }

    public function setEmploye(?employe $employe): self
    {
        $this->employe = $employe;

        return $this;
    }

    /**
     * @return Collection<int, Calendar>
     */
    public function getCalendars(): Collection
    {
        return $this->calendars;
    }

    public function addCalendar(Calendar $calendar): self
    {
        if (!$this->calendars->contains($calendar)) {
            $this->calendars[] = $calendar;
            $calendar->setDossier($this);
        }

        return $this;
    }

    public function removeCalendar(Calendar $calendar): self
    {
        if ($this->calendars->removeElement($calendar)) {
            // set the owning side to null (unless already changed)
            if ($calendar->getDossier() === $this) {
                $calendar->setDossier(null);
            }
        }

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

  

    /**
     * @return Collection<int, DocumentSigneFichier>
     */
    public function getDocumentSigneFichiers(): Collection
    {
        return $this->documentSigneFichiers;
    }

    public function addDocumentSigneFichier(DocumentSigneFichier $documentSigneFichier): self
    {
        if (!$this->documentSigneFichiers->contains($documentSigneFichier)) {
            $this->documentSigneFichiers->add($documentSigneFichier);
            $documentSigneFichier->setDossier($this);
        }

        return $this;
    }

    public function removeDocumentSigneFichier(DocumentSigneFichier $documentSigneFichier): self
    {
        if ($this->documentSigneFichiers->removeElement($documentSigneFichier)) {
            // set the owning side to null (unless already changed)
            if ($documentSigneFichier->getDossier() === $this) {
                $documentSigneFichier->setDossier(null);
            }
        }

        return $this;
    }

    // /**
    //  * @return Collection<int, CommentaireIdentification>
    //  */
    // public function getCommentaireIdentifications(): Collection
    // {
    //     return $this->CommentaireIdentifications;
    // }

    // public function addCommentaireIdentification(CommentaireIdentification $CommentaireIdentification): self
    // {
    //     if (!$this->CommentaireIdentifications->contains($CommentaireIdentification)) {
    //         $this->CommentaireIdentifications->add($CommentaireIdentification);
    //         $CommentaireIdentification->setDossier($this);
    //     }

    //     return $this;
    // }

    // public function removeCommentaireIdentification(CommentaireIdentification $CommentaireIdentification): self
    // {
    //     if ($this->CommentaireIdentifications->removeElement($CommentaireIdentification)) {
    //         // set the owning side to null (unless already changed)
    //         if ($CommentaireIdentification->getDossier() === $this) {
    //             $CommentaireIdentification->setDossier(null);
    //         }
    //     }

    //     return $this;
    // }

    /**
     * @return Collection<int, CommentairePiece>
     */
    public function getCommentairePieces(): Collection
    {
        return $this->commentairePieces;
    }

    public function addCommentairePiece(CommentairePiece $commentairePiece): self
    {
        if (!$this->commentairePieces->contains($commentairePiece)) {
            $this->commentairePieces->add($commentairePiece);
            $commentairePiece->setDossier($this);
        }

        return $this;
    }

    public function removeCommentairePiece(CommentairePiece $commentairePiece): self
    {
        if ($this->commentairePieces->removeElement($commentairePiece)) {
            // set the owning side to null (unless already changed)
            if ($commentairePiece->getDossier() === $this) {
                $commentairePiece->setDossier(null);
            }
        }

        return $this;
    }

    // /**
    //  * @return Collection<int, CommentairePaiement>
    //  */
    // public function getCommentairePaiements(): Collection
    // {
    //     return $this->commentairePaiements;
    // }

    // public function addCommentairePaiement(CommentairePaiement $commentairePaiement): self
    // {
    //     if (!$this->commentairePaiements->contains($commentairePaiement)) {
    //         $this->commentairePaiements->add($commentairePaiement);
    //         $commentairePaiement->setDossier($this);
    //     }

    //     return $this;
    // }

    // public function removeCommentairePaiement(CommentairePaiement $commentairePaiement): self
    // {
    //     if ($this->commentairePaiements->removeElement($commentairePaiement)) {
    //         // set the owning side to null (unless already changed)
    //         if ($commentairePaiement->getDossier() === $this) {
    //             $commentairePaiement->setDossier(null);
    //         }
    //     }

    //     return $this;
    // }

    /**
     * @return Collection<int, CommentaireRedaction>
     */
    public function getCommentaireRedactions(): Collection
    {
        return $this->commentaireRedactions;
    }

    public function addCommentaireRedaction(CommentaireRedaction $commentaireRedaction): self
    {
        if (!$this->commentaireRedactions->contains($commentaireRedaction)) {
            $this->commentaireRedactions->add($commentaireRedaction);
            $commentaireRedaction->setDossier($this);
        }

        return $this;
    }

    public function removeCommentaireRedaction(CommentaireRedaction $commentaireRedaction): self
    {
        if ($this->commentaireRedactions->removeElement($commentaireRedaction)) {
            // set the owning side to null (unless already changed)
            if ($commentaireRedaction->getDossier() === $this) {
                $commentaireRedaction->setDossier(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CommentaireSignature>
     */
    public function getCommentaireSignatures(): Collection
    {
        return $this->commentaireSignatures;
    }

    public function addCommentaireSignature(CommentaireSignature $commentaireSignature): self
    {
        if (!$this->commentaireSignatures->contains($commentaireSignature)) {
            $this->commentaireSignatures->add($commentaireSignature);
            $commentaireSignature->setDossier($this);
        }

        return $this;
    }

    public function removeCommentaireSignature(CommentaireSignature $commentaireSignature): self
    {
        if ($this->commentaireSignatures->removeElement($commentaireSignature)) {
            // set the owning side to null (unless already changed)
            if ($commentaireSignature->getDossier() === $this) {
                $commentaireSignature->setDossier(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CommentaireEng>
     */
    public function getCommentaireEngs(): Collection
    {
        return $this->commentaireEngs;
    }

    public function addCommentaireEng(CommentaireEng $commentaireEng): self
    {
        if (!$this->commentaireEngs->contains($commentaireEng)) {
            $this->commentaireEngs->add($commentaireEng);
            $commentaireEng->setDossier($this);
        }

        return $this;
    }

    public function removeCommentaireEng(CommentaireEng $commentaireEng): self
    {
        if ($this->commentaireEngs->removeElement($commentaireEng)) {
            // set the owning side to null (unless already changed)
            if ($commentaireEng->getDossier() === $this) {
                $commentaireEng->setDossier(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CommentaireObtention>
     */
    public function getCommentaireObtentions(): Collection
    {
        return $this->commentaireObtentions;
    }

    public function addCommentaireObtention(CommentaireObtention $commentaireObtention): self
    {
        if (!$this->commentaireObtentions->contains($commentaireObtention)) {
            $this->commentaireObtentions->add($commentaireObtention);
            $commentaireObtention->setDossier($this);
        }

        return $this;
    }

    public function removeCommentaireObtention(CommentaireObtention $commentaireObtention): self
    {
        if ($this->commentaireObtentions->removeElement($commentaireObtention)) {
            // set the owning side to null (unless already changed)
            if ($commentaireObtention->getDossier() === $this) {
                $commentaireObtention->setDossier(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EnregistrementDocument>
     */
    public function getEnregistrementDocuments(): Collection
    {
        return $this->enregistrementDocuments;
    }

    public function addEnregistrementDocument(EnregistrementDocument $enregistrementDocument): self
    {
        if (!$this->enregistrementDocuments->contains($enregistrementDocument)) {
            $this->enregistrementDocuments->add($enregistrementDocument);
            $enregistrementDocument->setDossier($this);
        }

        return $this;
    }

    public function removeEnregistrementDocument(EnregistrementDocument $enregistrementDocument): self
    {
        if ($this->enregistrementDocuments->removeElement($enregistrementDocument)) {
            // set the owning side to null (unless already changed)
            if ($enregistrementDocument->getDossier() === $this) {
                $enregistrementDocument->setDossier(null);
            }
        }

        return $this;
    }

    

    public function getNatureDossier(): ?string
    {
        return $this->natureDossier;
    }

    public function setNatureDossier(string $natureDossier): self
    {
        $this->natureDossier = $natureDossier;

        return $this;
    }

    public function getNumcompte(): ?string
    {
        return $this->numcompte;
    }

    public function setNumcompte(string $numcompte): static
    {
        $this->numcompte = $numcompte;

        return $this;
    }

}
