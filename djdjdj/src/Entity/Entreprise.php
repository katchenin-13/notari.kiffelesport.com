<?php

namespace App\Entity;

use App\Repository\EntrepriseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntrepriseRepository::class)]
#[ORM\Table(name: '_admin_param_entreprise')]
class Entreprise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $denomination = null;

    #[ORM\OneToMany(mappedBy: 'entreprise', targetEntity: Employe::class)]
    private Collection $employes;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\OneToMany(mappedBy: 'entreprise', targetEntity: ConfigApp::class)]
    private Collection $configApps;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Sigle = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Agrements = null;


    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $situation_geo = null;

    #[ORM\Column(length: 255)]
    private ?string $contacts = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse = null;


    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mobile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fax = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\ManyToOne(cascade: ["persist"], fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: true)]
    private ?FichierAdmin $logo = null;


    #[ORM\Column(length: 255)]
    private ?string $site_web = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Directeur = null;


    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\OneToMany(mappedBy: 'entreprise', targetEntity: CourierArrive::class)]
    private Collection $courierArrives;

    #[ORM\OneToMany(mappedBy: 'entreprise', targetEntity: Dossier::class)]
    private Collection $dossiers;

    #[ORM\OneToMany(mappedBy: 'entreprise', targetEntity: Fonction::class)]
    private Collection $fonctions;

    #[ORM\OneToMany(mappedBy: 'entreprise', targetEntity: Client::class)]
    private Collection $clients;

    #[ORM\OneToMany(mappedBy: 'entreprise', targetEntity: ActeConstitution::class)]
    private Collection $acteConstitutions;

    #[ORM\OneToMany(mappedBy: 'entreprise', targetEntity: Archive::class)]
    private Collection $archives;

    #[ORM\OneToMany(mappedBy: 'entreprise', targetEntity: Calendar::class)]
    private Collection $calendars;



    public function __construct()
    {
        $this->employes = new ArrayCollection();
        $this->configApps = new ArrayCollection();
        $this->courierArrives = new ArrayCollection();
        $this->dossiers = new ArrayCollection();
        $this->fonctions = new ArrayCollection();
        $this->clients = new ArrayCollection();
        $this->acteConstitutions = new ArrayCollection();
        $this->archives = new ArrayCollection();
        $this->calendars = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDenomination(): ?string
    {
        return $this->denomination;
    }

    public function setDenomination(string $denomination): self
    {
        $this->denomination = $denomination;

        return $this;
    }

    /**
     * @return Collection<int, Employe>
     */
    public function getEmployes(): Collection
    {
        return $this->employes;
    }

    public function addEmploye(Employe $employe): self
    {
        if (!$this->employes->contains($employe)) {
            $this->employes->add($employe);
            $employe->setEntreprise($this);
        }

        return $this;
    }

    public function removeEmploye(Employe $employe): self
    {
        if ($this->employes->removeElement($employe)) {
            // set the owning side to null (unless already changed)
            if ($employe->getEntreprise() === $this) {
                $employe->setEntreprise(null);
            }
        }

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
     * @return Collection<int, ConfigApp>
     */
    public function getConfigApps(): Collection
    {
        return $this->configApps;
    }

    public function addConfigApp(ConfigApp $configApp): static
    {
        if (!$this->configApps->contains($configApp)) {
            $this->configApps->add($configApp);
            $configApp->setEntreprise($this);
        }

        return $this;
    }

    public function removeConfigApp(ConfigApp $configApp): static
    {
        if ($this->configApps->removeElement($configApp)) {
            // set the owning side to null (unless already changed)
            if ($configApp->getEntreprise() === $this) {
                $configApp->setEntreprise(null);
            }
        }

        return $this;
    }




    public function getSigle(): ?string
    {
        return $this->Sigle;
    }

    public function setSigle(string $Sigle): static
    {
        $this->Sigle = $Sigle;

        return $this;
    }

    public function getAgrements(): ?string
    {
        return $this->Agrements;
    }

    public function setAgrements(string $Agrements): static
    {
        $this->Agrements = $Agrements;

        return $this;
    }


    public function getSituationGeo(): ?string
    {
        return $this->situation_geo;
    }

    public function setSituationGeo(string $situation_geo): static
    {
        $this->situation_geo = $situation_geo;

        return $this;
    }

    public function getContacts(): ?string
    {
        return $this->contacts;
    }

    public function setContacts(string $contacts): static
    {
        $this->contacts = $contacts;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }




    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(string $mobile): static
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(string $fax): static
    {
        $this->fax = $fax;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getLogo(): ?FichierAdmin
    {
        return $this->logo;
    }

    public function setLogo(FichierAdmin $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    public function getSiteWeb(): ?string
    {
        return $this->site_web;
    }

    public function setSiteWeb(string $site_web): static
    {
        $this->site_web = $site_web;

        return $this;
    }

    public function getDirecteur(): ?string
    {
        return $this->Directeur;
    }

    public function setDirecteur(string $Directeur): static
    {
        $this->Directeur = $Directeur;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * @return Collection<int, CourierArrive>
     */
    public function getCourierArrives(): Collection
    {
        return $this->courierArrives;
    }

    public function addCourierArrife(CourierArrive $courierArrife): static
    {
        if (!$this->courierArrives->contains($courierArrife)) {
            $this->courierArrives->add($courierArrife);
            $courierArrife->setEntreprise($this);
        }

        return $this;
    }

    public function removeCourierArrife(CourierArrive $courierArrife): static
    {
        if ($this->courierArrives->removeElement($courierArrife)) {
            // set the owning side to null (unless already changed)
            if ($courierArrife->getEntreprise() === $this) {
                $courierArrife->setEntreprise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Dossier>
     */
    public function getDossiers(): Collection
    {
        return $this->dossiers;
    }

    public function addDossier(Dossier $dossier): static
    {
        if (!$this->dossiers->contains($dossier)) {
            $this->dossiers->add($dossier);
            $dossier->setEntreprise($this);
        }

        return $this;
    }

    public function removeDossier(Dossier $dossier): static
    {
        if ($this->dossiers->removeElement($dossier)) {
            // set the owning side to null (unless already changed)
            if ($dossier->getEntreprise() === $this) {
                $dossier->setEntreprise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Fonction>
     */
    public function getFonctions(): Collection
    {
        return $this->fonctions;
    }

    public function addFonction(Fonction $fonction): static
    {
        if (!$this->fonctions->contains($fonction)) {
            $this->fonctions->add($fonction);
            $fonction->setEntreprise($this);
        }

        return $this;
    }

    public function removeFonction(Fonction $fonction): static
    {
        if ($this->fonctions->removeElement($fonction)) {
            // set the owning side to null (unless already changed)
            if ($fonction->getEntreprise() === $this) {
                $fonction->setEntreprise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Client>
     */
    public function getClients(): Collection
    {
        return $this->clients;
    }

    public function addClient(Client $client): static
    {
        if (!$this->clients->contains($client)) {
            $this->clients->add($client);
            $client->setEntreprise($this);
        }

        return $this;
    }

    public function removeClient(Client $client): static
    {
        if ($this->clients->removeElement($client)) {
            // set the owning side to null (unless already changed)
            if ($client->getEntreprise() === $this) {
                $client->setEntreprise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ActeConstitution>
     */
    public function getActeConstitutions(): Collection
    {
        return $this->acteConstitutions;
    }

    public function addActeConstitution(ActeConstitution $acteConstitution): static
    {
        if (!$this->acteConstitutions->contains($acteConstitution)) {
            $this->acteConstitutions->add($acteConstitution);
            $acteConstitution->setEntreprise($this);
        }

        return $this;
    }

    public function removeActeConstitution(ActeConstitution $acteConstitution): static
    {
        if ($this->acteConstitutions->removeElement($acteConstitution)) {
            // set the owning side to null (unless already changed)
            if ($acteConstitution->getEntreprise() === $this) {
                $acteConstitution->setEntreprise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Archive>
     */
    public function getArchives(): Collection
    {
        return $this->archives;
    }

    public function addArchive(Archive $archive): static
    {
        if (!$this->archives->contains($archive)) {
            $this->archives->add($archive);
            $archive->setEntreprise($this);
        }

        return $this;
    }

    public function removeArchive(Archive $archive): static
    {
        if ($this->archives->removeElement($archive)) {
            // set the owning side to null (unless already changed)
            if ($archive->getEntreprise() === $this) {
                $archive->setEntreprise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Calendar>
     */
    public function getCalendars(): Collection
    {
        return $this->calendars;
    }

    public function addCalendar(Calendar $calendar): static
    {
        if (!$this->calendars->contains($calendar)) {
            $this->calendars->add($calendar);
            $calendar->setEntreprise($this);
        }

        return $this;
    }

    public function removeCalendar(Calendar $calendar): static
    {
        if ($this->calendars->removeElement($calendar)) {
            // set the owning side to null (unless already changed)
            if ($calendar->getEntreprise() === $this) {
                $calendar->setEntreprise(null);
            }
        }

        return $this;
    }
}
