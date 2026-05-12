<?php

namespace App\Entity;

use App\Repository\EtablissementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtablissementRepository::class)]
class Etablissement
{
    
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue(strategy: "NONE")]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: false)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $code = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?int $licence = null;

    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $logo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $background = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $type = null;

    #[ORM\Column(nullable: true)]
    private ?int $msgbienvenu = null;

    #[ORM\Column(nullable: true)]
    private ?int $msgap = null;

    #[ORM\Column(nullable: true)]
    private ?int $accessTvInCheckout = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomEtablissement = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pays = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $genre = null;
    
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logoactive = null;
    
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $meteoactive = null;
    
    #[ORM\OneToMany(mappedBy: 'etablissement', targetEntity: ServiceEtablissement::class,cascade: ['persist'])]
    private Collection $ServiceEtablissement;
    
    #[ORM\OneToMany(mappedBy: 'etablissement', targetEntity: Categories::class)]
    private Collection $categories;

    #[ORM\OneToMany(mappedBy: 'etablissement', targetEntity: Services::class)]
    private Collection $services;


    #[ORM\OneToMany(mappedBy: 'etablissement', targetEntity: ServiceEnChambre::class)]
    private Collection $serviceEnChambre;

    #[ORM\OneToMany(mappedBy: 'etablissement', targetEntity: TypeServiceEnChambre::class)]
    private Collection $typeServiceEnChambre;
    
    #[ORM\OneToMany(mappedBy: 'etablissement', targetEntity: HistoriqueAnnonce::class)]
    private Collection $historiqueAnnonces;

    #[ORM\OneToMany(mappedBy: 'etablissement', targetEntity: Television::class)]
    private Collection $televisions;
    
    #[ORM\OneToMany(mappedBy: 'etablissement', targetEntity: CategorieRadio::class)]
    private Collection $categorieRadios;
    
    #[ORM\OneToMany(mappedBy: 'etablissement', targetEntity: Radio::class)]
    private Collection $radios;
    
    #[ORM\OneToMany(mappedBy: 'etablissement', targetEntity: Support::class)]
    private Collection $supports;
    
    #[ORM\OneToMany(mappedBy: 'etablissement', targetEntity: Application::class)]
    private Collection $applications;
    
    #[ORM\OneToMany(mappedBy: 'etablissement', targetEntity: Jeux::class)]
    private Collection $jeuxes;
    
    #[ORM\OneToMany(mappedBy: 'etablissement', targetEntity: CategorieVod::class)]
    private Collection $categorieVods;
    
    #[ORM\OneToMany(mappedBy: 'etablissement', targetEntity: CategorieLivreaudio::class)]
    private Collection $categorieLivreaudios;
    
    #[ORM\OneToMany(mappedBy: 'etablissement', targetEntity: Vod::class)]
    private Collection $vods;
    
    #[ORM\OneToMany(mappedBy: 'etablissement', targetEntity: Livreaudio::class)]
    private Collection $livreaudios;
    
    #[ORM\OneToMany(mappedBy: 'etablissement', targetEntity: Annonce::class)]
    private Collection $annonces;

    #[ORM\OneToMany(mappedBy: 'etablissement', targetEntity: Configmobile::class)]
    private Collection $configmobiles;
    
    #[ORM\OneToMany(mappedBy: 'etablissement', targetEntity: Questionnaire::class)]
    private Collection $questionnaires;
    
    
    #[ORM\OneToMany(mappedBy: 'etablissement', targetEntity: ResultatQuestionnaire::class)]
    private Collection $resultatQuestionnaires;

    #[ORM\OneToMany(mappedBy: 'etablissement', targetEntity: Chambre::class)]
    private Collection $chambres;

    #[ORM\OneToMany(mappedBy: 'etablissement', targetEntity: User::class)]
    private Collection $users;

    #[ORM\Column(length: 255)]
    private ?string $Typetext = null;

    #[ORM\Column(length: 255)]
    private ?string $Couleurtext = null;

    #[ORM\Column]
    private ?int $Tailletext = null;

    #[ORM\Column]
    private ?int $Volumedemarage = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $rss = null;
    
    public function __construct()
    {
        $this->ServiceEtablissement = new ArrayCollection();
        $this->categorieRadios = new ArrayCollection();
        $this->categorieLivreaudios = new ArrayCollection();
        $this->categorieVods = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->services = new ArrayCollection();
        $this->serviceEnChambre = new ArrayCollection();
        $this->typeServiceEnChambre = new ArrayCollection();
        $this->historiqueAnnonces = new ArrayCollection();
        $this->televisions = new ArrayCollection();
        $this->radios = new ArrayCollection();
        $this->livreaudios = new ArrayCollection();
        $this->vods = new ArrayCollection();
        $this->annonces = new ArrayCollection();
        $this->supports = new ArrayCollection();
        $this->applications = new ArrayCollection();
        $this->jeuxes = new ArrayCollection();
        $this->configmobiles = new ArrayCollection();
        $this->questionnaires = new ArrayCollection();
        $this->resultatQuestionnaires = new ArrayCollection();
        $this->chambres = new ArrayCollection();
        $this->users = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): static
    {
        $this->code = $code;

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

    public function getLicence(): ?int
    {
        return $this->licence;
    }

    public function setLicence(?int $licence): static
    {
        $this->licence = $licence;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    public function getBackground(): ?string
    {
        return $this->background;
    }

    public function setBackground(?string $background): static
    {
        $this->background = $background;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getMsgbienvenu(): ?int
    {
        return $this->msgbienvenu;
    }

    public function setMsgbienvenu(?int $msgbienvenu): static
    {
        $this->msgbienvenu = $msgbienvenu;

        return $this;
    }

    public function getMsgap(): ?int
    {
        return $this->msgap;
    }

    public function setMsgap(?int $msgap): static
    {
        $this->msgap = $msgap;

        return $this;
    }

    public function getAccessTvInCheckout(): ?int
    {
        return $this->accessTvInCheckout;
    }

    public function setAccessTvInCheckout(?int $access_tv_in_checkout): static
    {
        $this->accessTvInCheckout = $access_tv_in_checkout;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getNomEtablissement(): ?string
    {
        return $this->nomEtablissement;
    }

    public function setNomEtablissement(?string $nom_etablissement): static
    {
        $this->nomEtablissement = $nom_etablissement;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(?string $pays): static
    {
        $this->pays = $pays;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(?string $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setEtablissement($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getEtablissement() === $this) {
                $user->setEtablissement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Categories>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Categories $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->setEtablissement($this);
        }

        return $this;
    }

    public function removeCategory(Categories $category): static
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getEtablissement() === $this) {
                $category->setEtablissement(null);
            }
        }

        return $this;
    }

    public function getLogoactive(): ?string
    {
        return $this->logoactive;
    }

    public function setLogoactive(?string $logoactive): static
    {
        $this->logoactive = $logoactive;

        return $this;
    }

    public function getMeteoactive(): ?string
    {
        return $this->meteoactive;
    }

    public function setMeteoactive(?string $meteoactive): static
    {
        $this->meteoactive = $meteoactive;

        return $this;
    }

    /**
     * @return Collection<int, Television>
     */
    public function getTelevisions(): Collection
    {
        return $this->televisions;
    }

    public function addTelevision(Television $television): static
    {
        if (!$this->televisions->contains($television)) {
            $this->televisions->add($television);
            $television->setEtablissement($this);
        }

        return $this;
    }

    public function removeTelevision(Television $television): static
    {
        if ($this->televisions->removeElement($television)) {
            // set the owning side to null (unless already changed)
            if ($television->getEtablissement() === $this) {
                $television->setEtablissement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CategorieRadio>
     */
    public function getCategorieRadios(): Collection
    {
        return $this->categorieRadios;
    }

    public function addCategorieRadio(CategorieRadio $categorieRadio): static
    {
        if (!$this->categorieRadios->contains($categorieRadio)) {
            $this->categorieRadios->add($categorieRadio);
            $categorieRadio->setEtablissement($this);
        }

        return $this;
    }

    public function removeCategorieRadio(CategorieRadio $categorieRadio): static
    {
        if ($this->categorieRadios->removeElement($categorieRadio)) {
            // set the owning side to null (unless already changed)
            if ($categorieRadio->getEtablissement() === $this) {
                $categorieRadio->setEtablissement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Radio>
     */
    public function getRadios(): Collection
    {
        return $this->radios;
    }

    public function addRadio(Radio $radio): static
    {
        if (!$this->radios->contains($radio)) {
            $this->radios->add($radio);
            $radio->setEtablissement($this);
        }

        return $this;
    }

    public function removeRadio(Radio $radio): static
    {
        if ($this->radios->removeElement($radio)) {
            // set the owning side to null (unless already changed)
            if ($radio->getEtablissement() === $this) {
                $radio->setEtablissement(null);
            }
        }

        return $this;
    }

 

    /**
     * @return Collection<int, Support>
     */
    public function getSupports(): Collection
    {
        return $this->supports;
    }

    public function addSupport(Support $support): static
    {
        if (!$this->supports->contains($support)) {
            $this->supports->add($support);
            $support->setEtablissement($this);
        }

        return $this;
    }

    public function removeSupport(Support $support): static
    {
        if ($this->supports->removeElement($support)) {
            // set the owning side to null (unless already changed)
            if ($support->getEtablissement() === $this) {
                $support->setEtablissement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Application>
     */
    public function getApplications(): Collection
    {
        return $this->applications;
    }

    public function addApplication(Application $application): static
    {
        if (!$this->applications->contains($application)) {
            $this->applications->add($application);
            $application->setEtablissement($this);
        }

        return $this;
    }

    public function removeApplication(Application $application): static
    {
        if ($this->applications->removeElement($application)) {
            // set the owning side to null (unless already changed)
            if ($application->getEtablissement() === $this) {
                $application->setEtablissement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Jeux>
     */
    public function getJeuxes(): Collection
    {
        return $this->jeuxes;
    }

    public function addJeux(Jeux $jeux): static
    {
        if (!$this->jeuxes->contains($jeux)) {
            $this->jeuxes->add($jeux);
            $jeux->setEtablissement($this);
        }

        return $this;
    }

    public function removeJeux(Jeux $jeux): static
    {
        if ($this->jeuxes->removeElement($jeux)) {
            // set the owning side to null (unless already changed)
            if ($jeux->getEtablissement() === $this) {
                $jeux->setEtablissement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CategorieLivreaudio>
     */
    public function getCategorieLivreaudios(): Collection
    {
        return $this->categorieLivreaudios;
    }

    public function addCategorieLivreaudio(CategorieLivreaudio $categorieLivreaudio): static
    {
        if (!$this->categorieLivreaudios->contains($categorieLivreaudio)) {
            $this->categorieLivreaudios->add($categorieLivreaudio);
            $categorieLivreaudio->setEtablissement($this);
        }

        return $this;
    }

    public function removeCategorieLivreaudio(CategorieLivreaudio $categorieLivreaudio): static
    {
        if ($this->categorieLivreaudios->removeElement($categorieLivreaudio)) {
            // set the owning side to null (unless already changed)
            if ($categorieLivreaudio->getEtablissement() === $this) {
                $categorieLivreaudio->setEtablissement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Livreaudio>
     */
    public function getLivreaudios(): Collection
    {
        return $this->livreaudios;
    }

    public function addLivreaudio(Livreaudio $livreaudio): static
    {
        if (!$this->livreaudios->contains($livreaudio)) {
            $this->livreaudios->add($livreaudio);
            $livreaudio->setEtablissement($this);
        }

        return $this;
    }

    public function removeLivreaudio(Livreaudio $livreaudio): static
    {
        if ($this->livreaudios->removeElement($livreaudio)) {
            // set the owning side to null (unless already changed)
            if ($livreaudio->getEtablissement() === $this) {
                $livreaudio->setEtablissement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Configmobile>
     */
    public function getConfigmobiles(): Collection
    {
        return $this->configmobiles;
    }

    public function addConfigmobile(Configmobile $configmobile): static
    {
        if (!$this->configmobiles->contains($configmobile)) {
            $this->configmobiles->add($configmobile);
            $configmobile->setEtablissement($this);
        }

        return $this;
    }

    public function removeConfigmobile(Configmobile $configmobile): static
    {
        if ($this->configmobiles->removeElement($configmobile)) {
            // set the owning side to null (unless already changed)
            if ($configmobile->getEtablissement() === $this) {
                $configmobile->setEtablissement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Questionnaire>
     */
    public function getQuestionnaires(): Collection
    {
        return $this->questionnaires;
    }

    public function addQuestionnaire(Questionnaire $questionnaire): static
    {
        if (!$this->questionnaires->contains($questionnaire)) {
            $this->questionnaires->add($questionnaire);
            $questionnaire->setEtablissement($this);
        }

        return $this;
    }

    public function removeQuestionnaire(Questionnaire $questionnaire): static
    {
        if ($this->questionnaires->removeElement($questionnaire)) {
            // set the owning side to null (unless already changed)
            if ($questionnaire->getEtablissement() === $this) {
                $questionnaire->setEtablissement(null);
            }
        }

        return $this;
    }



    /**
     * @return Collection<int, ResultatQuestionnaire>
     */
    public function getResultatQuestionnaires(): Collection
    {
        return $this->resultatQuestionnaires;
    }

    public function addResultatQuestionnaire(ResultatQuestionnaire $resultatQuestionnaire): static
    {
        if (!$this->resultatQuestionnaires->contains($resultatQuestionnaire)) {
            $this->resultatQuestionnaires->add($resultatQuestionnaire);
            $resultatQuestionnaire->setEtablissement($this);
        }

        return $this;
    }

    public function removeResultatQuestionnaire(ResultatQuestionnaire $resultatQuestionnaire): static
    {
        if ($this->resultatQuestionnaires->removeElement($resultatQuestionnaire)) {
            // set the owning side to null (unless already changed)
            if ($resultatQuestionnaire->getEtablissement() === $this) {
                $resultatQuestionnaire->setEtablissement(null);
            }
        }

        return $this;
    }
    /**
     * @return Collection<int, Services>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }
    public function addServices(Services $Services): static
    {
        if (!$this->services->contains($Services)) {
            $this->services->add($Services);
            $Services->setEtablissement($this);
        }

        return $this;
    }

    public function removeServices(Services $Services): static
    {
        if ($this->services->removeElement($Services)) {
            // set the owning side to null (unless already changed)
            if ($Services->getEtablissement() === $this) {
                $Services->setEtablissement(null);
            }
        }
        return $this;
    }


  /**
     * @return Collection<int, ServiceEnChambre>
     */
    public function getServiceEnChambre(): Collection
    {
        return $this->serviceEnChambre;
    }
    public function addServiceEnChambre(ServiceEnChambre $ServiceEnChambre): static
    {
        if (!$this->serviceEnChambre->contains($ServiceEnChambre)) {
            $this->serviceEnChambre->add($ServiceEnChambre);
            $ServiceEnChambre->setEtablissement($this);
        }

        return $this;
    }

    public function removeServiceEnChambre(ServiceEnChambre $ServiceEnChambre): static
    {
        if ($this->serviceEnChambre->removeElement($ServiceEnChambre)) {
            // set the owning side to null (unless already changed)
            if ($ServiceEnChambre->getEtablissement() === $this) {
                $ServiceEnChambre->setEtablissement(null);
            }
        }
        return $this;
    }



    /**
     * @return Collection<int, TypeServiceEnChambre>
     */
    public function getTypeServiceEnChambre(): Collection
    {
        return $this->typeServiceEnChambre;
    }
    public function addTypeServiceEnChambre(TypeServiceEnChambre $TypeServiceEnChambre): static
    {
        if (!$this->typeServiceEnChambre->contains($TypeServiceEnChambre)) {
            $this->typeServiceEnChambre->add($TypeServiceEnChambre);
            $TypeServiceEnChambre->setEtablissement($this);
        }

        return $this;
    }

    public function removeTypeServiceEnChambre(TypeServiceEnChambre $TypeServiceEnChambre): static
    {
        if ($this->typeServiceEnChambre->removeElement($TypeServiceEnChambre)) {
            // set the owning side to null (unless already changed)
            if ($TypeServiceEnChambre->getEtablissement() === $this) {
                $TypeServiceEnChambre->setEtablissement(null);
            }
        }
        return $this;
    }


     /**
     * @return Collection<int, HistoriqueAnnonce>
     */
    public function getHistoriqueAnnonce(): Collection
    {
        return $this->historiqueAnnonces;
    }
    public function addHistoriqueAnnonce(HistoriqueAnnonce $historiqueAnnonces): static
    {
        if (!$this->historiqueAnnonces->contains($historiqueAnnonces)) {
            $this->historiqueAnnonces->add($historiqueAnnonces);
            $historiqueAnnonces->setEtablissement($this);
        }

        return $this;
    }

    public function removeHistoriqueAnnonce(HistoriqueAnnonce $historiqueAnnonces): static
    {
        if ($this->historiqueAnnonces->removeElement($historiqueAnnonces)) {
            // set the owning side to null (unless already changed)
            if ($historiqueAnnonces->getEtablissement() === $this) {
                $historiqueAnnonces->setEtablissement(null);
            }
        }
        return $this;
    }

     /**
     * @return Collection<int, ServiceEtablissement>
     */
    public function getServiceEtablissement(): Collection
    {
        return $this->ServiceEtablissement;
    }

    public function addServiceEtablissement(ServiceEtablissement $ServiceEtablissement): static
    {
        if (!$this->ServiceEtablissement->contains($ServiceEtablissement)) {
            $this->ServiceEtablissement->add($ServiceEtablissement);
            $ServiceEtablissement->setEtablissement($this);
        }

        return $this;
    }

    public function removeServiceEtablissement(ServiceEtablissement $ServiceEtablissement): static
    {
        if ($this->ServiceEtablissement->removeElement($ServiceEtablissement)) {
            // set the owning side to null (unless already changed)
            if ($ServiceEtablissement->getEtablissement() === $this) {
                $ServiceEtablissement->setEtablissement(null);
            }
        }

        return $this;
    }

       /**
     * @return Collection<int, Chambre>
     */
    public function getChambres(): Collection
    {
        return $this->chambres;
    }

    public function addChambres(Chambre $Chambres): static
    {
        if (!$this->chambres->contains($Chambres)) {
            $this->chambres->add($Chambres);
            $Chambres->setEtablissement($this);
        }

        return $this;
    }

    public function removeChamberes(Chambre $Chambres): static
    {
        if ($this->chambres->removeElement($Chambres)) {
            // set the owning side to null (unless already changed)
            if ($Chambres->getEtablissement() === $this) {
                $Chambres->setEtablissement(null);
            }
        }

        return $this;
    }





        /**
     * @return Collection<int, Annonce>
     */
    public function getAnnonces(): Collection
    {
        return $this->annonces;
    }

    public function addAnnonces(Annonce $annonce): static
    {
        if (!$this->annonces->contains($annonce)) {
            $this->annonces->add($annonce);
            $annonce->setEtablissement($this);
        }

        return $this;
    }

    public function removeAnnonces(Annonce $annonce): static
    {
        if ($this->annonces->removeElement($annonce)) {
            // set the owning side to null (unless already changed)
            if ($annonce->getEtablissement() === $this) {
                $annonce->setEtablissement(null);
            }
        }
        return $this;
    }


    /**
     * @return Collection<int, CategorieVod>
     */
    public function getCategorievods(): Collection
    {
        return $this->categorieVods;
    }

    public function addCategorievod(CategorieVod $categorievod): static
    {
        if (!$this->categorieVods->contains($categorievod)) {
            $this->categorieVods->add($categorievod);
            $categorievod->setEtablissement($this);
        }
        return $this;
    }
    public function removeCategorievod(CategorieVod $categorievod): static
    {
        if ($this->categorieVods->removeElement($categorievod)) {
            // set the owning side to null (unless already changed)
            if ($categorievod->getEtablissement() === $this) {
                $categorievod->setEtablissement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Vod>
     */
    public function getvods(): Collection
    {
        return $this->vods;
    }

    public function addvod(Vod $vod): static
    {
        if (!$this->vods->contains($vod)) {
            $this->vods->add($vod);
            $vod->setEtablissement($this);
        }

        return $this;
    }

    public function removevod(Vod $vod): static
    {
        if ($this->vods->removeElement($vod)) {
            // set the owning side to null (unless already changed)
            if ($vod->getEtablissement() === $this) {
                $vod->setEtablissement(null);
            }
        }

        return $this;
    }

    public function getTypeText(): ?string
    {
        return $this->Typetext;
    }

    public function setTypeText(string $type_text): static
    {
        $this->Typetext = $type_text;

        return $this;
    }

    public function getCouleurText(): ?string
    {
        return $this->Couleurtext;
    }

    public function setCouleurText(string $couleur_text): static
    {
        $this->Couleurtext = $couleur_text;

        return $this;
    }

    public function getTailleText(): ?int
    {
        return $this->Tailletext;
    }

    public function setTailleText(int $taille_text): static
    {
        $this->Tailletext = $taille_text;

        return $this;
    }

    public function getVolumeDemarage(): ?int
    {
        return $this->Volumedemarage;
    }

    public function setVolumeDemarage(int $volume_demarage): static
    {
        $this->Volumedemarage = $volume_demarage;

        return $this;
    }

    public function getRss(): ?string
    {
        return $this->rss;
    }

    public function setRss(?string $rss): static
    {
        $this->rss = $rss;

        return $this;
    }




}
