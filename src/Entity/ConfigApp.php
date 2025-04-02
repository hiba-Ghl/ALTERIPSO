<?php

namespace App\Entity;

use App\Repository\ConfigAppRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConfigAppRepository::class)]
class ConfigApp
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Etablissement $etablissement = null;

    #[ORM\Column(length: 255)]
    private ?string $ServerHostRsmartv = null;

    #[ORM\Column(length: 255)]
    private ?string $ServerHostPaytv = null;

    #[ORM\Column(length: 255)]
    private ?string $ServerHostVod = null;

    #[ORM\Column(length: 255)]
    private ?string $ServerHostLivreaudio = null;

    #[ORM\Column(length: 255)]
    private ?string $ServerHostToukan = null;

    #[ORM\Column(length: 255)]
    private ?string $ServerHostCanalplus = null;

    #[ORM\Column(length: 255)]
    private ?string $ServerHostMail = null;

    #[ORM\Column(length: 255)]
    private ?string $DatabaseNameRsmartv = null;

    #[ORM\Column(length: 255)]
    private ?string $DatabaseUserRsmartv = null;

    #[ORM\Column(length: 255)]
    private ?string $DatabasePassRsmartv = null;

    #[ORM\Column(length: 255)]
    private ?string $DatabaseNamePaytv = null;

    #[ORM\Column(length: 255)]
    private ?string $DatabaseUserPaytv = null;

    #[ORM\Column(length: 255)]
    private ?string $DatabasePassPaytv = null;

    #[ORM\Column(length: 255)]
    private ?string $DatabaseNameVod = null;

    #[ORM\Column(length: 255)]
    private ?string $DatabaseUserVod = null;

    #[ORM\Column(length: 255)]
    private ?string $DatabasePassVod = null;

    #[ORM\Column(length: 255)]
    private ?string $DatabaseNameCanalplus = null;

    #[ORM\Column(length: 255)]
    private ?string $DatabaseUserCanalplus = null;

    #[ORM\Column(length: 255)]
    private ?string $DatabasePassCanalplus = null;

    #[ORM\Column(length: 255)]
    private ?string $EnableTELEVISION = null;

    #[ORM\Column(length: 255)]
    private ?string $EnableSTATISTIQUECHAINETV = null;

    #[ORM\Column(length: 255)]
    private ?string $EnableRADIO = null;

    #[ORM\Column(length: 255)]
    private ?string $EnableSERVICE = null;

    #[ORM\Column(length: 255)]
    private ?string $EnableVOD = null;

    #[ORM\Column(length: 255)]
    private ?string $EnableMUSIQUE = null;

    #[ORM\Column(length: 255)]
    private ?string $EnableLIVREAUDIO = null;

    #[ORM\Column(length: 255)]
    private ?string $EnableJEUX = null;

    #[ORM\Column(length: 255)]
    private ?string $EnableSERVICESPAYANTS = null;

    #[ORM\Column(length: 255)]
    private ?string $EnableQUESTIONNAIRE = null;

    #[ORM\Column(length: 255)]
    private ?string $EnableAPPLICATION = null;

    #[ORM\Column(length: 255)]
    private ?string $EnableENREGISTREMENT = null;

    #[ORM\Column(length: 255)]
    private ?string $EnableANNONCES = null;

    #[ORM\Column(length: 255)]
    private ?string $EnableCHARTES = null;

    #[ORM\Column(length: 255)]
    private ?string $EnableVIDEOS = null;

    #[ORM\Column(length: 255)]
    private ?string $EnableSupportConnect = null;

    #[ORM\Column(length: 255)]
    private ?string $EnableMessagePersonnels = null;

    #[ORM\Column(length: 255)]
    private ?string $EnableRApplication = null;

    #[ORM\Column(length: 255)]
    private ?string $EnableCategories = null;

    #[ORM\Column(length: 255)]
    private ?string $CHECKIN = null;

    #[ORM\Column(length: 255)]
    private ?string $CHECKOUT = null;

    #[ORM\Column(length: 255)]
    private ?string $CODEPORTAIL = null;

    #[ORM\Column(length: 255)]
    private ?string $StatusServeur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getServerHostRsmartv(): ?string
    {
        return $this->ServerHostRsmartv;
    }

    public function setServerHostRsmartv(string $ServerHostRsmartv): static
    {
        $this->ServerHostRsmartv = $ServerHostRsmartv;

        return $this;
    }

    public function getServerHostPaytv(): ?string
    {
        return $this->ServerHostPaytv;
    }

    public function setServerHostPaytv(string $ServerHostPaytv): static
    {
        $this->ServerHostPaytv = $ServerHostPaytv;

        return $this;
    }

    public function getServerHostVod(): ?string
    {
        return $this->ServerHostVod;
    }

    public function setServerHostVod(string $ServerHostVod): static
    {
        $this->ServerHostVod = $ServerHostVod;

        return $this;
    }

    public function getServerHostLivreaudio(): ?string
    {
        return $this->ServerHostLivreaudio;
    }

    public function setServerHostLivreaudio(string $ServerHostLivreaudio): static
    {
        $this->ServerHostLivreaudio = $ServerHostLivreaudio;

        return $this;
    }

    public function getServerHostToukan(): ?string
    {
        return $this->ServerHostToukan;
    }

    public function setServerHostToukan(string $ServerHostToukan): static
    {
        $this->ServerHostToukan = $ServerHostToukan;

        return $this;
    }

    public function getServerHostCanalplus(): ?string
    {
        return $this->ServerHostCanalplus;
    }

    public function setServerHostCanalplus(string $ServerHostCanalplus): static
    {
        $this->ServerHostCanalplus = $ServerHostCanalplus;

        return $this;
    }

    public function getServerHostMail(): ?string
    {
        return $this->ServerHostMail;
    }

    public function setServerHostMail(string $ServerHostMail): static
    {
        $this->ServerHostMail = $ServerHostMail;

        return $this;
    }

    public function getDatabaseNameRsmartv(): ?string
    {
        return $this->DatabaseNameRsmartv;
    }

    public function setDatabaseNameRsmartv(string $DatabaseNameRsmartv): static
    {
        $this->DatabaseNameRsmartv = $DatabaseNameRsmartv;

        return $this;
    }

    public function getDatabaseUserRsmartv(): ?string
    {
        return $this->DatabaseUserRsmartv;
    }

    public function setDatabaseUserRsmartv(string $DatabaseUserRsmartv): static
    {
        $this->DatabaseUserRsmartv = $DatabaseUserRsmartv;

        return $this;
    }

    public function getDatabasePassRsmartv(): ?string
    {
        return $this->DatabasePassRsmartv;
    }

    public function setDatabasePassRsmartv(string $DatabasePassRsmartv): static
    {
        $this->DatabasePassRsmartv = $DatabasePassRsmartv;

        return $this;
    }

    public function getDatabaseNamePaytv(): ?string
    {
        return $this->DatabaseNamePaytv;
    }

    public function setDatabaseNamePaytv(string $DatabaseNamePaytv): static
    {
        $this->DatabaseNamePaytv = $DatabaseNamePaytv;

        return $this;
    }

    public function getDatabaseUserPaytv(): ?string
    {
        return $this->DatabaseUserPaytv;
    }

    public function setDatabaseUserPaytv(string $DatabaseUserPaytv): static
    {
        $this->DatabaseUserPaytv = $DatabaseUserPaytv;

        return $this;
    }

    public function getDatabasePassPaytv(): ?string
    {
        return $this->DatabasePassPaytv;
    }

    public function setDatabasePassPaytv(string $DatabasePassPaytv): static
    {
        $this->DatabasePassPaytv = $DatabasePassPaytv;

        return $this;
    }

    public function getDatabaseNameVod(): ?string
    {
        return $this->DatabaseNameVod;
    }

    public function setDatabaseNameVod(string $DatabaseNameVod): static
    {
        $this->DatabaseNameVod = $DatabaseNameVod;

        return $this;
    }

    public function getDatabaseUserVod(): ?string
    {
        return $this->DatabaseUserVod;
    }

    public function setDatabaseUserVod(string $DatabaseUserVod): static
    {
        $this->DatabaseUserVod = $DatabaseUserVod;

        return $this;
    }

    public function getDatabasePassVod(): ?string
    {
        return $this->DatabasePassVod;
    }

    public function setDatabasePassVod(string $DatabasePassVod): static
    {
        $this->DatabasePassVod = $DatabasePassVod;

        return $this;
    }

    public function getDatabaseNameCanalplus(): ?string
    {
        return $this->DatabaseNameCanalplus;
    }

    public function setDatabaseNameCanalplus(string $DatabaseNameCanalplus): static
    {
        $this->DatabaseNameCanalplus = $DatabaseNameCanalplus;

        return $this;
    }

    public function getDatabaseUserCanalplus(): ?string
    {
        return $this->DatabaseUserCanalplus;
    }

    public function setDatabaseUserCanalplus(string $DatabaseUserCanalplus): static
    {
        $this->DatabaseUserCanalplus = $DatabaseUserCanalplus;

        return $this;
    }

    public function getDatabasePassCanalplus(): ?string
    {
        return $this->DatabasePassCanalplus;
    }

    public function setDatabasePassCanalplus(string $DatabasePassCanalplus): static
    {
        $this->DatabasePassCanalplus = $DatabasePassCanalplus;

        return $this;
    }

    public function getEnableTELEVISION(): ?string
    {
        return $this->EnableTELEVISION;
    }

    public function setEnableTELEVISION(string $EnableTELEVISION): static
    {
        $this->EnableTELEVISION = $EnableTELEVISION;

        return $this;
    }

    public function getEnableSTATISTIQUECHAINETV(): ?string
    {
        return $this->EnableSTATISTIQUECHAINETV;
    }

    public function setEnableSTATISTIQUECHAINETV(string $EnableSTATISTIQUECHAINETV): static
    {
        $this->EnableSTATISTIQUECHAINETV = $EnableSTATISTIQUECHAINETV;

        return $this;
    }

    public function getEnableRADIO(): ?string
    {
        return $this->EnableRADIO;
    }

    public function setEnableRADIO(string $EnableRADIO): static
    {
        $this->EnableRADIO = $EnableRADIO;

        return $this;
    }

    public function getEnableSERVICE(): ?string
    {
        return $this->EnableSERVICE;
    }

    public function setEnableSERVICE(string $EnableSERVICE): static
    {
        $this->EnableSERVICE = $EnableSERVICE;

        return $this;
    }

    public function getEnableVOD(): ?string
    {
        return $this->EnableVOD;
    }

    public function setEnableVOD(string $EnableVOD): static
    {
        $this->EnableVOD = $EnableVOD;

        return $this;
    }

    public function getEnableMUSIQUE(): ?string
    {
        return $this->EnableMUSIQUE;
    }

    public function setEnableMUSIQUE(string $EnableMUSIQUE): static
    {
        $this->EnableMUSIQUE = $EnableMUSIQUE;

        return $this;
    }

    public function getEnableLIVREAUDIO(): ?string
    {
        return $this->EnableLIVREAUDIO;
    }

    public function setEnableLIVREAUDIO(string $EnableLIVREAUDIO): static
    {
        $this->EnableLIVREAUDIO = $EnableLIVREAUDIO;

        return $this;
    }

    public function getEnableJEUX(): ?string
    {
        return $this->EnableJEUX;
    }

    public function setEnableJEUX(string $EnableJEUX): static
    {
        $this->EnableJEUX = $EnableJEUX;

        return $this;
    }

    public function getEnableSERVICESPAYANTS(): ?string
    {
        return $this->EnableSERVICESPAYANTS;
    }

    public function setEnableSERVICESPAYANTS(string $EnableSERVICESPAYANTS): static
    {
        $this->EnableSERVICESPAYANTS = $EnableSERVICESPAYANTS;

        return $this;
    }

    public function getEnableQUESTIONNAIRE(): ?string
    {
        return $this->EnableQUESTIONNAIRE;
    }

    public function setEnableQUESTIONNAIRE(string $EnableQUESTIONNAIRE): static
    {
        $this->EnableQUESTIONNAIRE = $EnableQUESTIONNAIRE;

        return $this;
    }

    public function getEnableAPPLICATION(): ?string
    {
        return $this->EnableAPPLICATION;
    }

    public function setEnableAPPLICATION(string $EnableAPPLICATION): static
    {
        $this->EnableAPPLICATION = $EnableAPPLICATION;

        return $this;
    }

    public function getEnableENREGISTREMENT(): ?string
    {
        return $this->EnableENREGISTREMENT;
    }

    public function setEnableENREGISTREMENT(string $EnableENREGISTREMENT): static
    {
        $this->EnableENREGISTREMENT = $EnableENREGISTREMENT;

        return $this;
    }

    public function getEnableANNONCES(): ?string
    {
        return $this->EnableANNONCES;
    }

    public function setEnableANNONCES(string $EnableANNONCES): static
    {
        $this->EnableANNONCES = $EnableANNONCES;

        return $this;
    }

    public function getEnableCHARTES(): ?string
    {
        return $this->EnableCHARTES;
    }

    public function setEnableCHARTES(string $EnableCHARTES): static
    {
        $this->EnableCHARTES = $EnableCHARTES;

        return $this;
    }

    public function getEnableVIDEOS(): ?string
    {
        return $this->EnableVIDEOS;
    }

    public function setEnableVIDEOS(string $EnableVIDEOS): static
    {
        $this->EnableVIDEOS = $EnableVIDEOS;

        return $this;
    }

    public function getEnableSupportConnect(): ?string
    {
        return $this->EnableSupportConnect;
    }

    public function setEnableSupportConnect(string $EnableSupportConnect): static
    {
        $this->EnableSupportConnect = $EnableSupportConnect;

        return $this;
    }

    public function getEnableMessagePersonnels(): ?string
    {
        return $this->EnableMessagePersonnels;
    }

    public function setEnableMessagePersonnels(string $EnableMessagePersonnels): static
    {
        $this->EnableMessagePersonnels = $EnableMessagePersonnels;

        return $this;
    }

    public function getEnableRApplication(): ?string
    {
        return $this->EnableRApplication;
    }

    public function setEnableRApplication(string $EnableRApplication): static
    {
        $this->EnableRApplication = $EnableRApplication;

        return $this;
    }

    public function getEnableCategories(): ?string
    {
        return $this->EnableCategories;
    }

    public function setEnableCategories(string $EnableCategories): static
    {
        $this->EnableCategories = $EnableCategories;

        return $this;
    }

    public function getCHECKIN(): ?string
    {
        return $this->CHECKIN;
    }

    public function setCHECKIN(string $CHECKIN): static
    {
        $this->CHECKIN = $CHECKIN;

        return $this;
    }

    public function getCHECKOUT(): ?string
    {
        return $this->CHECKOUT;
    }

    public function setCHECKOUT(string $CHECKOUT): static
    {
        $this->CHECKOUT = $CHECKOUT;

        return $this;
    }

    public function getCODEPORTAIL(): ?string
    {
        return $this->CODEPORTAIL;
    }

    public function setCODEPORTAIL(string $CODEPORTAIL): static
    {
        $this->CODEPORTAIL = $CODEPORTAIL;

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
    public function getStatusServeur(): ?string
    {
        return $this->StatusServeur;
    }

    public function setStatusServeur(string $StatusServeur): static
    {
        $this->StatusServeur = $StatusServeur;

        return $this;
    }
}
