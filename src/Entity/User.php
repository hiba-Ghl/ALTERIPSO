<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(type: 'string', length: 100)]
    private $resetToken;


    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etablissement $etablissement = null;

    #[ORM\Column]
    private ?bool $TELEVISION = null;

    #[ORM\Column]
    private ?bool $STATISTIQUECHAINETV = null;

    #[ORM\Column]
    private ?bool $RADIO = null;

    #[ORM\Column]
    private ?bool $SERVICE = null;

    #[ORM\Column]
    private ?bool $VOD = null;

    #[ORM\Column]
    private ?bool $MUSIQUE = null;

    #[ORM\Column]
    private ?bool $LIVREAUDIO = null;

    #[ORM\Column]
    private ?bool $JEUX = null;

    #[ORM\Column]
    private ?bool $SERVICESPAYANTS = null;

    #[ORM\Column]
    private ?bool $QUESTIONNAIRE = null;

    #[ORM\Column]
    private ?bool $APPLICATION = null;

    #[ORM\Column]
    private ?bool $ANNONCES = null;

    #[ORM\Column]
    private ?bool $CHARTES = null;

    #[ORM\Column]
    private ?bool $VIDEOS = null;

    #[ORM\Column]
    private ?bool $SupportConnect = null;

    #[ORM\Column]
    private ?bool $MessagePersonnel = null;

    #[ORM\Column]
    private ?bool $RMOBILE = null;

    #[ORM\Column]
    private ?bool $RREMOTE = null;

    #[ORM\Column]
    private ?bool $AjoutTV = null;

    #[ORM\Column]
    private ?bool $ModifierTv = null;

    #[ORM\Column]
    private ?bool $SupprimerTV = null;

    #[ORM\Column]
    private ?bool $SauvegarderTv = null;

    #[ORM\Column]
    private ?bool $GratuiteTv = null;

    #[ORM\Column]
    private ?bool $AjoutRadio = null;

    #[ORM\Column]
    private ?bool $ModifierRadio = null;

    #[ORM\Column]
    private ?bool $SupprimerRadio = null;

    #[ORM\Column]
    private ?bool $SauvegarderRadio = null;

    #[ORM\Column]
    private ?bool $AjouteService = null;

    #[ORM\Column]
    private ?bool $ModifierService = null;

    #[ORM\Column]
    private ?bool $SupprimerService = null;

    #[ORM\Column]
    private ?bool $SauvegarderService = null;

    #[ORM\Column]
    private ?bool $LancerArretService = null;

    #[ORM\Column]
    private ?bool $AjouteVod = null;

    #[ORM\Column]
    private ?bool $ModifierVod = null;

    #[ORM\Column]
    private ?bool $SupprimerVod = null;

    #[ORM\Column]
    private ?bool $SauvegarderVod = null;

    #[ORM\Column]
    private ?bool $LancerVod = null;

    #[ORM\Column]
    private ?bool $AjouterJeux = null;

    #[ORM\Column]
    private ?bool $ModifierJeux = null;

    #[ORM\Column]
    private ?bool $SupprimerJeux = null;

    #[ORM\Column]
    private ?bool $SauvegarderJeux = null;

    #[ORM\Column]
    private ?bool $AjoutApp = null;

    #[ORM\Column]
    private ?bool $ModifierApp = null;

    #[ORM\Column]
    private ?bool $SupprimerApp = null;

    #[ORM\Column]
    private ?bool $SauvegarderApp = null;

    #[ORM\Column]
    private ?bool $Ajouteqs = null;

    #[ORM\Column]
    private ?bool $Modifierqs = null;

    #[ORM\Column]
    private ?bool $Supprimerqs = null;

    #[ORM\Column]
    private ?bool $Sauvgarderqs = null;

    #[ORM\Column]
    private ?bool $AjoutLiveAudio = null;

    #[ORM\Column]
    private ?bool $ModifierLiveAudio = null;

    #[ORM\Column]
    private ?bool $SupprimerLiveAudio = null;

    #[ORM\Column]
    private ?bool $SauvegarderLiveAudio = null;

    #[ORM\Column]
    private ?bool $AjouteSupport = null;

    #[ORM\Column]
    private ?bool $ModifierSupport = null;

    #[ORM\Column]
    private ?bool $SupprimerSupport = null;

    #[ORM\Column]
    private ?bool $RedimarerSupport = null;

    #[ORM\Column]
    private ?bool $EnvoyerMessage = null;

    #[ORM\Column]
    private ?bool $MjTv = null;

    #[ORM\Column]
    private ?bool $ChangeCat = null;

    #[ORM\Column]
    private ?bool $AjouterAnnonce = null;

    #[ORM\Column]
    private ?bool $ModifierAnnonce = null;

    #[ORM\Column]
    private ?bool $SuppAnnonce = null;

    #[ORM\Column]
    private ?bool $SauvegarderAnnonce = null;

    #[ORM\Column]
    private ?bool $AjoutCategorie = null;

    #[ORM\Column]
    private ?bool $SauvegarderAcceuil = null;

    #[ORM\Column]
    private ?bool $FondEcran = null;

    #[ORM\Column]
    private ?bool $lancerTV = null;

    #[ORM\Column]
    private ?bool $ajoutMusique = null;

    #[ORM\Column]
    private ?bool $ModifierMusique = null;

    #[ORM\Column]
    private ?bool $SuppMusique = null;

    #[ORM\Column]
    private ?bool $SauvegarderMusique = null;

    #[ORM\Column]
    private ?bool $lancerMusique = null;

    #[ORM\Column]
    private ?bool $lancerJeux = null;

    #[ORM\Column]
    private ?bool $lancerVideo = null;

    #[ORM\Column]
    private ?bool $lancerLivreAudio = null;

    #[ORM\Column]
    private ?bool $ajoutVideo = null;

    #[ORM\Column]
    private ?bool $ModifierVideo = null;

    #[ORM\Column]
    private ?bool $SauvegarderVideo = null;

    #[ORM\Column]
    private ?bool $suppVideo = null;

    #[ORM\Column]
    private ?bool $lancerRadio = null;

    #[ORM\Column]
    private ?bool $ajoutCatLivreAudio = null;

    #[ORM\Column]
    private ?bool $ModifierCatLivreAudio = null;

    #[ORM\Column]
    private ?bool $SuppCatLivreAudio = null;

    #[ORM\Column]
    private ?bool $SauvegarderCatLivreAudio = null;

    #[ORM\Column]
    private ?bool $AjoutCatRadio = null;

    #[ORM\Column]
    private ?bool $ModifierCatRadio = null;

    #[ORM\Column]
    private ?bool $SuppCatRadio = null;

    #[ORM\Column]
    private ?bool $SauvegarderCatRadio = null;

    #[ORM\Column]
    private ?bool $AjoutCatVod = null;

    #[ORM\Column]
    private ?bool $ModifierCatVod = null;

    #[ORM\Column]
    private ?bool $SuppCatVod = null;

    #[ORM\Column]
    private ?bool $SauvegarderCatVod = null;

    #[ORM\Column]
    private ?bool $AjoutServiceEnChambre = null;

    #[ORM\Column]
    private ?bool $ModifierServiceEnChambre = null;

    #[ORM\Column]
    private ?bool $SuppServiceEnChambre = null;

    #[ORM\Column]
    private ?bool $SauvegarderServiceEnChambre = null;

    #[ORM\Column]
    private ?bool $CatLivreAudio = null;

    #[ORM\Column]
    private ?bool $CatRadio = null;

    #[ORM\Column]
    private ?bool $CatVod = null;

    #[ORM\Column]
    private ?bool $ServiceEnChambre = null;

    #[ORM\Column]
    private ?bool $ResultatQs = null;

    #[ORM\Column(length: 255)]
    private ?string $EmailAdmin = null;

    #[ORM\Column]
    private ?int $tentativeOblierMdp = 0;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dernierTemp = null;

    #[ORM\Column]
    private ?bool $SauvgarderServicePayant = null;

    #[ORM\Column]
    private ?bool $SauvegarderChartPatient = null;

    #[ORM\Column]
    private ?bool $CocherMeteo = null;

    #[ORM\Column]
    private ?bool $CocherLogo = null;

    #[ORM\Column]
    private ?bool $ModifierRmobile = null;

    #[ORM\Column]
    private ?bool $AjouteServiceEtablissement = null;

    #[ORM\Column]
    private ?bool $CheckSupport = null;

    #[ORM\Column]
    private ?int $tentativeExport = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $derniertempExport = null;

    #[ORM\Column]
    private ?bool $ImportRadio = null;

    #[ORM\Column]
    private ?bool $ExportRadio = null;

    #[ORM\Column]
    private ?bool $ImportTV = null;

    #[ORM\Column]
    private ?bool $ExportTv = null;
      
    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(string $id): static
    {
        $this->id = $id;

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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        // $roles[] = 'ROLE_ADMIN';
        

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }
    
    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;
    
        return $this;
    }
    public function getEtablissement(): ?Etablissement
    {
        return $this->etablissement;
    }

    public function setEtablissement(?Etablissement $etablissement): static
    {
        $this->etablissement = $etablissement;

        return $this;
    }

    public function getTELEVISION(): ?bool
    {
        return $this->TELEVISION;
    }

    public function setTELEVISION(bool $TELEVISION): static
    {
        $this->TELEVISION = $TELEVISION;

        return $this;
    }

    public function getSTATISTIQUECHAINETV(): ?bool
    {
        return $this->STATISTIQUECHAINETV;
    }

    public function setSTATISTIQUECHAINETV(bool $STATISTIQUECHAINETV): static
    {
        $this->STATISTIQUECHAINETV = $STATISTIQUECHAINETV;

        return $this;
    }

    public function getRADIO(): ?bool
    {
        return $this->RADIO;
    }

    public function setRADIO(bool $RADIO): static
    {
        $this->RADIO = $RADIO;

        return $this;
    }

    public function getSERVICE(): ?bool
    {
        return $this->SERVICE;
    }

    public function setSERVICE(bool $SERVICE): static
    {
        $this->SERVICE = $SERVICE;

        return $this;
    }

    public function getVOD(): ?bool
    {
        return $this->VOD;
    }

    public function setVOD(bool $VOD): static
    {
        $this->VOD = $VOD;

        return $this;
    }

    public function getMUSIQUE(): ?bool
    {
        return $this->MUSIQUE;
    }

    public function setMUSIQUE(bool $MUSIQUE): static
    {
        $this->MUSIQUE = $MUSIQUE;

        return $this;
    }

    public function getLIVREAUDIO(): ?bool
    {
        return $this->LIVREAUDIO;
    }

    public function setLIVREAUDIO(bool $LIVREAUDIO): static
    {
        $this->LIVREAUDIO = $LIVREAUDIO;

        return $this;
    }

    public function getJEUX(): ?bool
    {
        return $this->JEUX;
    }

    public function setJEUX(bool $JEUX): static
    {
        $this->JEUX = $JEUX;

        return $this;
    }

    public function getSERVICESPAYANTS(): ?bool
    {
        return $this->SERVICESPAYANTS;
    }

    public function setSERVICESPAYANTS(bool $SERVICESPAYANTS): static
    {
        $this->SERVICESPAYANTS = $SERVICESPAYANTS;

        return $this;
    }

    public function getQUESTIONNAIRE(): ?bool
    {
        return $this->QUESTIONNAIRE;
    }

    public function setQUESTIONNAIRE(bool $QUESTIONNAIRE): static
    {
        $this->QUESTIONNAIRE = $QUESTIONNAIRE;

        return $this;
    }

    public function getAPPLICATION(): ?bool
    {
        return $this->APPLICATION;
    }

    public function setAPPLICATION(bool $APPLICATION): static
    {
        $this->APPLICATION = $APPLICATION;

        return $this;
    }

    public function getANNONCES(): ?bool
    {
        return $this->ANNONCES;
    }

    public function setANNONCES(bool $ANNONCES): static
    {
        $this->ANNONCES = $ANNONCES;

        return $this;
    }

    public function getCHARTES(): ?bool
    {
        return $this->CHARTES;
    }

    public function setCHARTES(bool $CHARTES): static
    {
        $this->CHARTES = $CHARTES;

        return $this;
    }

    public function getVIDEOS(): ?bool
    {
        return $this->VIDEOS;
    }

    public function setVIDEOS(bool $VIDEOS): static
    {
        $this->VIDEOS = $VIDEOS;

        return $this;
    }

    public function getSupportConnect(): ?bool
    {
        return $this->SupportConnect;
    }

    public function setSupportConnect(bool $SupportConnect): static
    {
        $this->SupportConnect = $SupportConnect;

        return $this;
    }

    public function getMessagePersonnel(): ?bool
    {
        return $this->MessagePersonnel;
    }

    public function setMessagePersonnel(bool $MessagePersonnel): static
    {
        $this->MessagePersonnel = $MessagePersonnel;

        return $this;
    }

    public function getRMOBILE(): ?bool
    {
        return $this->RMOBILE;
    }

    public function setRMOBILE(bool $RMOBILE): static
    {
        $this->RMOBILE = $RMOBILE;

        return $this;
    }

    public function getRREMOTE(): ?bool
    {
        return $this->RREMOTE;
    }

    public function setRREMOTE(bool $RREMOTE): static
    {
        $this->RREMOTE = $RREMOTE;

        return $this;
    }

    public function getAjoutTV(): ?bool
    {
        return $this->AjoutTV;
    }

    public function setAjoutTV(bool $AjoutTV): static
    {
        $this->AjoutTV = $AjoutTV;

        return $this;
    }

    public function getModifierTv(): ?bool
    {
        return $this->ModifierTv;
    }

    public function setModifierTv(bool $ModifierTv): static
    {
        $this->ModifierTv = $ModifierTv;

        return $this;
    }

    public function getSupprimerTV(): ?bool
    {
        return $this->SupprimerTV;
    }

    public function setSupprimerTV(bool $SupprimerTV): static
    {
        $this->SupprimerTV = $SupprimerTV;

        return $this;
    }

    public function getSauvegarderTv(): ?bool
    {
        return $this->SauvegarderTv;
    }

    public function setSauvegarderTv(bool $SauvegarderTv): static
    {
        $this->SauvegarderTv = $SauvegarderTv;

        return $this;
    }

    public function getGratuiteTv(): ?bool
    {
        return $this->GratuiteTv;
    }

    public function setGratuiteTv(bool $GratuiteTv): static
    {
        $this->GratuiteTv = $GratuiteTv;

        return $this;
    }

    public function getAjoutRadio(): ?bool
    {
        return $this->AjoutRadio;
    }

    public function setAjoutRadio(bool $AjoutRadio): static
    {
        $this->AjoutRadio = $AjoutRadio;

        return $this;
    }

    public function getModifierRadio(): ?bool
    {
        return $this->ModifierRadio;
    }

    public function setModifierRadio(bool $ModifierRadio): static
    {
        $this->ModifierRadio = $ModifierRadio;

        return $this;
    }

    public function getSupprimerRadio(): ?bool
    {
        return $this->SupprimerRadio;
    }

    public function setSupprimerRadio(bool $SupprimerRadio): static
    {
        $this->SupprimerRadio = $SupprimerRadio;

        return $this;
    }

    public function getSauvegarderRadio(): ?bool
    {
        return $this->SauvegarderRadio;
    }

    public function setSauvegarderRadio(bool $SauvegarderRadio): static
    {
        $this->SauvegarderRadio = $SauvegarderRadio;

        return $this;
    }

    public function getAjouteService(): ?bool
    {
        return $this->AjouteService;
    }

    public function setAjouteService(bool $AjouteService): static
    {
        $this->AjouteService = $AjouteService;

        return $this;
    }

    public function getModifierService(): ?bool
    {
        return $this->ModifierService;
    }

    public function setModifierService(bool $ModifierService): static
    {
        $this->ModifierService = $ModifierService;

        return $this;
    }

    public function getSupprimerService(): ?bool
    {
        return $this->SupprimerService;
    }

    public function setSupprimerService(bool $SupprimerService): static
    {
        $this->SupprimerService = $SupprimerService;

        return $this;
    }

    public function getSauvegarderService(): ?bool
    {
        return $this->SauvegarderService;
    }

    public function setSauvegarderService(bool $SauvegarderService): static
    {
        $this->SauvegarderService = $SauvegarderService;

        return $this;
    }

    public function getLancerArretService(): ?bool
    {
        return $this->LancerArretService;
    }

    public function setLancerArretService(bool $LancerArretService): static
    {
        $this->LancerArretService = $LancerArretService;

        return $this;
    }

    public function getAjouteVod(): ?bool
    {
        return $this->AjouteVod;
    }

    public function setAjouteVod(bool $AjouteVod): static
    {
        $this->AjouteVod = $AjouteVod;

        return $this;
    }

    public function getModifierVod(): ?bool
    {
        return $this->ModifierVod;
    }

    public function setModifierVod(bool $ModifierVod): static
    {
        $this->ModifierVod = $ModifierVod;

        return $this;
    }

    public function getSupprimerVod(): ?bool
    {
        return $this->SupprimerVod;
    }

    public function setSupprimerVod(bool $SupprimerVod): static
    {
        $this->SupprimerVod = $SupprimerVod;

        return $this;
    }

    public function getSauvegarderVod(): ?bool
    {
        return $this->SauvegarderVod;
    }

    public function setSauvegarderVod(bool $SauvegarderVod): static
    {
        $this->SauvegarderVod = $SauvegarderVod;

        return $this;
    }

    public function getLancerVod(): ?bool
    {
        return $this->LancerVod;
    }

    public function setLancerVod(bool $LancerVod): static
    {
        $this->LancerVod = $LancerVod;

        return $this;
    }

    public function getAjouterJeux(): ?bool
    {
        return $this->AjouterJeux;
    }

    public function setAjouterJeux(bool $AjouterJeux): static
    {
        $this->AjouterJeux = $AjouterJeux;

        return $this;
    }

    public function getModifierJeux(): ?bool
    {
        return $this->ModifierJeux;
    }

    public function setModifierJeux(bool $ModifierJeux): static
    {
        $this->ModifierJeux = $ModifierJeux;

        return $this;
    }

    public function getSupprimerJeux(): ?bool
    {
        return $this->SupprimerJeux;
    }

    public function setSupprimerJeux(bool $SupprimerJeux): static
    {
        $this->SupprimerJeux = $SupprimerJeux;

        return $this;
    }

    public function getSauvegarderJeux(): ?bool
    {
        return $this->SauvegarderJeux;
    }

    public function setSauvegarderJeux(bool $SauvegarderJeux): static
    {
        $this->SauvegarderJeux = $SauvegarderJeux;

        return $this;
    }

    public function getAjoutApp(): ?bool
    {
        return $this->AjoutApp;
    }

    public function setAjoutApp(bool $AjoutApp): static
    {
        $this->AjoutApp = $AjoutApp;

        return $this;
    }

    public function getModifierApp(): ?bool
    {
        return $this->ModifierApp;
    }

    public function setModifierApp(bool $ModifierApp): static
    {
        $this->ModifierApp = $ModifierApp;

        return $this;
    }

    public function getSupprimerApp(): ?bool
    {
        return $this->SupprimerApp;
    }

    public function setSupprimerApp(bool $SupprimerApp): static
    {
        $this->SupprimerApp = $SupprimerApp;

        return $this;
    }

    public function getSauvegarderApp(): ?bool
    {
        return $this->SauvegarderApp;
    }

    public function setSauvegarderApp(bool $SauvegarderApp): static
    {
        $this->SauvegarderApp = $SauvegarderApp;

        return $this;
    }

    public function getAjouteqs(): ?bool
    {
        return $this->Ajouteqs;
    }

    public function setAjouteqs(bool $Ajouteqs): static
    {
        $this->Ajouteqs = $Ajouteqs;

        return $this;
    }

    public function getModifierqs(): ?bool
    {
        return $this->Modifierqs;
    }

    public function setModifierqs(bool $Modifierqs): static
    {
        $this->Modifierqs = $Modifierqs;

        return $this;
    }

    public function getSupprimerqs(): ?bool
    {
        return $this->Supprimerqs;
    }

    public function setSupprimerqs(bool $Supprimerqs): static
    {
        $this->Supprimerqs = $Supprimerqs;

        return $this;
    }

    public function getSauvgarderqs(): ?bool
    {
        return $this->Sauvgarderqs;
    }

    public function setSauvgarderqs(bool $Sauvgarderqs): static
    {
        $this->Sauvgarderqs = $Sauvgarderqs;

        return $this;
    }

    public function getAjoutLiveAudio(): ?bool
    {
        return $this->AjoutLiveAudio;
    }

    public function setAjoutLiveAudio(bool $AjoutLiveAudio): static
    {
        $this->AjoutLiveAudio = $AjoutLiveAudio;

        return $this;
    }

    public function getModifierLiveAudio(): ?bool
    {
        return $this->ModifierLiveAudio;
    }

    public function setModifierLiveAudio(bool $ModifierLiveAudio): static
    {
        $this->ModifierLiveAudio = $ModifierLiveAudio;

        return $this;
    }

    public function getSupprimerLiveAudio(): ?bool
    {
        return $this->SupprimerLiveAudio;
    }

    public function setSupprimerLiveAudio(bool $SupprimerLiveAudio): static
    {
        $this->SupprimerLiveAudio = $SupprimerLiveAudio;

        return $this;
    }

    public function getSauvegarderLiveAudio(): ?bool
    {
        return $this->SauvegarderLiveAudio;
    }

    public function setSauvegarderLiveAudio(bool $SauvegarderLiveAudio): static
    {
        $this->SauvegarderLiveAudio = $SauvegarderLiveAudio;

        return $this;
    }

    public function getAjouteSupport(): ?bool
    {
        return $this->AjouteSupport;
    }

    public function setAjouteSupport(bool $AjouteSupport): static
    {
        $this->AjouteSupport = $AjouteSupport;

        return $this;
    }

    public function getModifierSupport(): ?bool
    {
        return $this->ModifierSupport;
    }

    public function setModifierSupport(bool $ModifierSupport): static
    {
        $this->ModifierSupport = $ModifierSupport;

        return $this;
    }

    public function getSupprimerSupport(): ?bool
    {
        return $this->SupprimerSupport;
    }

    public function setSupprimerSupport(bool $SupprimerSupport): static
    {
        $this->SupprimerSupport = $SupprimerSupport;

        return $this;
    }

    public function getRedimarerSupport(): ?bool
    {
        return $this->RedimarerSupport;
    }

    public function setRedimarerSupport(bool $RedimarerSupport): static
    {
        $this->RedimarerSupport = $RedimarerSupport;

        return $this;
    }

    public function getEnvoyerMessage(): ?bool
    {
        return $this->EnvoyerMessage; 
    }

    public function setEnvoyerMessage(bool $EnvoyerMessage): static
    {
        $this->EnvoyerMessage = $EnvoyerMessage;

        return $this;
    }

    public function getMjTv(): ?bool
    {
        return $this->MjTv;
    }

    public function setMjTv(bool $MjTv): static
    {
        $this->MjTv = $MjTv;

        return $this;
    }

    public function getChangeCat(): ?bool
    {
        return $this->ChangeCat;
    }

    public function setChangeCat(bool $ChangeCat): static
    {
        $this->ChangeCat = $ChangeCat;

        return $this;
    }

    public function getAjouterAnnonce(): ?bool
    {
        return $this->AjouterAnnonce;
    }

    public function setAjouterAnnonce(bool $AjouterAnnonce): static
    {
        $this->AjouterAnnonce = $AjouterAnnonce;

        return $this;
    }

    public function getModifierAnnonce(): ?bool
    {
        return $this->ModifierAnnonce;
    }

    public function setModifierAnnonce(bool $ModifierAnnonce): static
    {
        $this->ModifierAnnonce = $ModifierAnnonce;

        return $this;
    }

    public function getSuppAnnonce(): ?bool
    {
        return $this->SuppAnnonce;
    }

    public function setSuppAnnonce(bool $SuppAnnonce): static
    {
        $this->SuppAnnonce = $SuppAnnonce;

        return $this;
    }

    public function getSauvegarderAnnonce(): ?bool
    {
        return $this->SauvegarderAnnonce;
    }

    public function setSauvegarderAnnonce(bool $SauvegarderAnnonce): static
    {
        $this->SauvegarderAnnonce = $SauvegarderAnnonce;

        return $this;
    }

    public function getAjoutCategorie(): ?bool
    {
        return $this->AjoutCategorie;
    }

    public function setAjoutCategorie(bool $te): static
    {
        $this->AjoutCategorie = $te;

        return $this;
    }

    public function isSauvegarderAcceuil(): ?bool
    {
        return $this->SauvegarderAcceuil;
    }

    public function setSauvegarderAcceuil(bool $SauvegarderAcceuil): static
    {
        $this->SauvegarderAcceuil = $SauvegarderAcceuil;

        return $this;
    }

    public function isFondEcran(): ?bool
    {
        return $this->FondEcran;
    }

    public function setFondEcran(bool $FondEcran): static
    {
        $this->FondEcran = $FondEcran;

        return $this;
    }

    public function isLancerTV(): ?bool
    {
        return $this->lancerTV;
    }

    public function setLancerTV(bool $lancerTV): static
    {
        $this->lancerTV = $lancerTV;

        return $this;
    }

    public function isAjoutMusique(): ?bool
    {
        return $this->ajoutMusique;
    }

    public function setAjoutMusique(bool $ajoutMusique): static
    {
        $this->ajoutMusique = $ajoutMusique;

        return $this;
    }

    public function isModifierMusique(): ?bool
    {
        return $this->ModifierMusique;
    }

    public function setModifierMusique(bool $ModifierMusique): static
    {
        $this->ModifierMusique = $ModifierMusique;

        return $this;
    }

    public function isSuppMusique(): ?bool
    {
        return $this->SuppMusique;
    }

    public function setSuppMusique(bool $SuppMusique): static
    {
        $this->SuppMusique = $SuppMusique;

        return $this;
    }

    public function isSauvegarderMusique(): ?bool
    {
        return $this->SauvegarderMusique;
    }

    public function setSauvegarderMusique(bool $SauvegarderMusique): static
    {
        $this->SauvegarderMusique = $SauvegarderMusique;

        return $this;
    }

    public function isLancerMusique(): ?bool
    {
        return $this->lancerMusique;
    }

    public function setLancerMusique(bool $lancerMusique): static
    {
        $this->lancerMusique = $lancerMusique;

        return $this;
    }

    public function isLancerJeux(): ?bool
    {
        return $this->lancerJeux;
    }

    public function setLancerJeux(bool $lancerJeux): static
    {
        $this->lancerJeux = $lancerJeux;

        return $this;
    }

    public function isLancerVideo(): ?bool
    {
        return $this->lancerVideo;
    }

    public function setLancerVideo(bool $lancerVideo): static
    {
        $this->lancerVideo = $lancerVideo;

        return $this;
    }

    public function isLancerLivreAudio(): ?bool
    {
        return $this->lancerLivreAudio;
    }

    public function setLancerLivreAudio(bool $lancerLivreAudio): static
    {
        $this->lancerLivreAudio = $lancerLivreAudio;

        return $this;
    }

    public function isAjoutVideo(): ?bool
    {
        return $this->ajoutVideo;
    }

    public function setAjoutVideo(bool $ajoutVideo): static
    {
        $this->ajoutVideo = $ajoutVideo;

        return $this;
    }

    public function isModifierVideo(): ?bool
    {
        return $this->ModifierVideo;
    }

    public function setModifierVideo(bool $ModifierVideo): static
    {
        $this->ModifierVideo = $ModifierVideo;

        return $this;
    }

    public function isSauvegarderVideo(): ?bool
    {
        return $this->SauvegarderVideo;
    }

    public function setSauvegarderVideo(bool $SauvegarderVideo): static
    {
        $this->SauvegarderVideo = $SauvegarderVideo;

        return $this;
    }

    public function isSuppVideo(): ?bool
    {
        return $this->suppVideo;
    }

    public function setSuppVideo(bool $suppVideo): static
    {
        $this->suppVideo = $suppVideo;

        return $this;
    }

    public function isLancerRadio(): ?bool
    {
        return $this->lancerRadio;
    }

    public function setLancerRadio(bool $lancerRadio): static
    {
        $this->lancerRadio = $lancerRadio;

        return $this;
    }

    public function isAjoutCatLivreAudio(): ?bool
    {
        return $this->ajoutCatLivreAudio;
    }

    public function setAjoutCatLivreAudio(bool $ajoutCatLivreAudio): static
    {
        $this->ajoutCatLivreAudio = $ajoutCatLivreAudio;

        return $this;
    }

    public function isModifierCatLivreAudio(): ?bool
    {
        return $this->ModifierCatLivreAudio;
    }

    public function setModifierCatLivreAudio(bool $ModifierCatLivreAudio): static
    {
        $this->ModifierCatLivreAudio = $ModifierCatLivreAudio;

        return $this;
    }

    public function isSuppCatLivreAudio(): ?bool
    {
        return $this->SuppCatLivreAudio;
    }

    public function setSuppCatLivreAudio(bool $SuppCatLivreAudio): static
    {
        $this->SuppCatLivreAudio = $SuppCatLivreAudio;

        return $this;
    }

    public function isSauvegarderCatLivreAudio(): ?bool
    {
        return $this->SauvegarderCatLivreAudio;
    }

    public function setSauvegarderCatLivreAudio(bool $SauvegarderCatLivreAudio): static
    {
        $this->SauvegarderCatLivreAudio = $SauvegarderCatLivreAudio;

        return $this;
    }

    public function isAjoutCatRadio(): ?bool
    {
        return $this->AjoutCatRadio;
    }

    public function setAjoutCatRadio(bool $AjoutCatRadio): static
    {
        $this->AjoutCatRadio = $AjoutCatRadio;

        return $this;
    }

    public function isModifierCatRadio(): ?bool
    {
        return $this->ModifierCatRadio;
    }

    public function setModifierCatRadio(bool $ModifierCatRadio): static
    {
        $this->ModifierCatRadio = $ModifierCatRadio;

        return $this;
    }

    public function isSuppCatRadio(): ?bool
    {
        return $this->SuppCatRadio;
    }

    public function setSuppCatRadio(bool $SuppCatRadio): static
    {
        $this->SuppCatRadio = $SuppCatRadio;

        return $this;
    }

    public function isSauvegarderCatRadio(): ?bool
    {
        return $this->SauvegarderCatRadio;
    }

    public function setSauvegarderCatRadio(bool $SauvegarderCatRadio): static
    {
        $this->SauvegarderCatRadio = $SauvegarderCatRadio;

        return $this;
    }

    public function isAjoutCatVod(): ?bool
    {
        return $this->AjoutCatVod;
    }

    public function setAjoutCatVod(bool $AjoutCatVod): static
    {
        $this->AjoutCatVod = $AjoutCatVod;

        return $this;
    }

    public function isModifierCatVod(): ?bool
    {
        return $this->ModifierCatVod;
    }

    public function setModifierCatVod(bool $ModifierCatVod): static
    {
        $this->ModifierCatVod = $ModifierCatVod;

        return $this;
    }

    public function isSuppCatVod(): ?bool
    {
        return $this->SuppCatVod;
    }

    public function setSuppCatVod(bool $SuppCatVod): static
    {
        $this->SuppCatVod = $SuppCatVod;

        return $this;
    }

    public function isSauvegarderCatVod(): ?bool
    {
        return $this->SauvegarderCatVod;
    }

    public function setSauvegarderCatVod(bool $SauvegarderCatVod): static
    {
        $this->SauvegarderCatVod = $SauvegarderCatVod;

        return $this;
    }

    public function isAjoutServiceEnChambre(): ?bool
    {
        return $this->AjoutServiceEnChambre;
    }

    public function setAjoutServiceEnChambre(bool $AjoutServiceEnChambre): static
    {
        $this->AjoutServiceEnChambre = $AjoutServiceEnChambre;

        return $this;
    }

    public function isModifierServiceEnChambre(): ?bool
    {
        return $this->ModifierServiceEnChambre;
    }

    public function setModifierServiceEnChambre(bool $ModifierServiceEnChambre): static
    {
        $this->ModifierServiceEnChambre = $ModifierServiceEnChambre;

        return $this;
    }

    public function isSuppServiceEnChambre(): ?bool
    {
        return $this->SuppServiceEnChambre;
    }

    public function setSuppServiceEnChambre(bool $SuppServiceEnChambre): static
    {
        $this->SuppServiceEnChambre = $SuppServiceEnChambre;

        return $this;
    }

    public function isSauvegarderServiceEnChambre(): ?bool
    {
        return $this->SauvegarderServiceEnChambre;
    }

    public function setSauvegarderServiceEnChambre(bool $SauvegarderServiceEnChambre): static
    {
        $this->SauvegarderServiceEnChambre = $SauvegarderServiceEnChambre;

        return $this;
    }

    public function isCatLivreAudio(): ?bool
    {
        return $this->CatLivreAudio;
    }

    public function setCatLivreAudio(bool $CatLivreAudio): static
    {
        $this->CatLivreAudio = $CatLivreAudio;

        return $this;
    }

    public function isCatRadio(): ?bool
    {
        return $this->CatRadio;
    }

    public function setCatRadio(bool $CatRadio): static
    {
        $this->CatRadio = $CatRadio;

        return $this;
    }

    public function isCatVod(): ?bool
    {
        return $this->CatVod;
    }

    public function setCatVod(bool $CatVod): static
    {
        $this->CatVod = $CatVod;

        return $this;
    }

    public function isServiceEnChambre(): ?bool
    {
        return $this->ServiceEnChambre;
    }

    public function setServiceEnChambre(bool $ServiceEnChambre): static
    {
        $this->ServiceEnChambre = $ServiceEnChambre;

        return $this;
    }

    public function isResultatQs(): ?bool
    {
        return $this->ResultatQs;
    }

    public function setResultatQs(bool $ResultatQs): static
    {
        $this->ResultatQs = $ResultatQs;

        return $this;
    }

    public function getEmailAdmin(): ?string
    {
        return $this->EmailAdmin;
    }

    public function setEmailAdmin(string $EmailAdmin): static
    {
        $this->EmailAdmin = $EmailAdmin;

        return $this;
    }

    public function getTentativeOblierMdp(): ?int
    {
        return $this->tentativeOblierMdp;
    }

    public function setTentativeOblierMdp(int $tentativeOblierMdp): static
    {
        $this->tentativeOblierMdp = $tentativeOblierMdp;

        return $this;
    }

    public function getdernierTemp(): ?\DateTimeInterface
    {
        return $this->dernierTemp;
    }

    public function setdernierTemp(\DateTimeInterface $dernierTemp): static
    {
        $this->dernierTemp = $dernierTemp;

        return $this;
    }

    public function isSauvgarderServicePayant(): ?bool
    {
        return $this->SauvgarderServicePayant;
    }

    public function setSauvgarderServicePayant(bool $SauvgarderServicePayant): static
    {
        $this->SauvgarderServicePayant = $SauvgarderServicePayant;

        return $this;
    }

    public function isSauvegarderChartPatient(): ?bool
    {
        return $this->SauvegarderChartPatient;
    }

    public function setSauvegarderChartPatient(bool $SauvegarderChartPatient): static
    {
        $this->SauvegarderChartPatient = $SauvegarderChartPatient;

        return $this;
    }

    public function isCocherMeteo(): ?bool
    {
        return $this->CocherMeteo;
    }

    public function setCocherMeteo(bool $CocherMeteo): static
    {
        $this->CocherMeteo = $CocherMeteo;

        return $this;
    }

    public function isCocherLogo(): ?bool
    {
        return $this->CocherLogo;
    }

    public function setCocherLogo(bool $CocherLogo): static
    {
        $this->CocherLogo = $CocherLogo;

        return $this;
    }
    public function isModifierRmobile(): ?bool
    {
        return $this->ModifierRmobile;
    }

    public function setModifierRmobile(bool $ModifierRmobile): static
    {
        $this->ModifierRmobile = $ModifierRmobile;

        return $this;
    }

    public function isAjouteServiceEtablissement(): ?bool
    {
        return $this->AjouteServiceEtablissement;
    }

    public function setAjouteServiceEtablissement(bool $AjouteServiceEtablissement): static
    {
        $this->AjouteServiceEtablissement = $AjouteServiceEtablissement;

        return $this;
    }

    public function isCheckSupport(): ?bool
    {
        return $this->CheckSupport;
    }

    public function setCheckSupport(bool $CheckSupport): static
    {
        $this->CheckSupport = $CheckSupport;

        return $this;
    }

    public function getTentativeExport(): ?int
    {
        return $this->tentativeExport;
    }

    public function setTentativeExport(int $tentativeExport): static
    {
        $this->tentativeExport = $tentativeExport;

        return $this;
    }

    public function getDerniertempExport(): ?\DateTimeInterface
    {
        return $this->derniertempExport;
    }

    public function setDerniertempExport(\DateTimeInterface $derniertempExport): static
    {
        $this->derniertempExport = $derniertempExport;

        return $this;
    }


    public function isImportRadio(): ?bool
    {
        return $this->ImportRadio;
    }

    public function setImportRadio(bool $ImportRadio): static
    {
        $this->ImportRadio = $ImportRadio;

        return $this;
    }

    public function isExportRadio(): ?bool
    {
        return $this->ExportRadio;
    }

    public function setExportRadio(bool $ExportRadio): static
    {
        $this->ExportRadio = $ExportRadio;

        return $this;
    }

    public function isImportTV(): ?bool
    {
        return $this->ImportTV;
    }

    public function setImportTV(bool $ImportTV): static
    {
        $this->ImportTV = $ImportTV;

        return $this;
    }

    public function isExportTv(): ?bool
    {
        return $this->ExportTv;
    }

    public function setExportTv(bool $ExportTv): static
    {
        $this->ExportTv = $ExportTv;

        return $this;
    }
  
}
