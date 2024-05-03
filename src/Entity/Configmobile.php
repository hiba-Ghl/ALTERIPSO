<?php

namespace App\Entity;

use App\Repository\ConfigmobileRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConfigmobileRepository::class)]
class Configmobile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'configmobiles')]
    private ?Etablissement $etablissement = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $img_affichage = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $img_logo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $info_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $info_adresse = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $info_cp = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $info_ville = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $info_pays = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $info_tel = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $info_siret = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $info_email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $info_chambre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $date_version = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $date_valeurs = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $facture_objet = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $facture_title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $facture_footer = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phase = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $payement_version = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $payement_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $payement_key = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $payement_vad = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $payement_pk = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $payement_sk = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prix_casque = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getImgAffichage(): ?string
    {
        return $this->img_affichage;
    }

    public function setImgAffichage(?string $img_affichage): static
    {
        $this->img_affichage = $img_affichage;

        return $this;
    }

    public function getImgLogo(): ?string
    {
        return $this->img_logo;
    }

    public function setImgLogo(?string $img_logo): static
    {
        $this->img_logo = $img_logo;

        return $this;
    }

    public function getInfoId(): ?string
    {
        return $this->info_id;
    }

    public function setInfoId(?string $info_id): static
    {
        $this->info_id = $info_id;

        return $this;
    }

    public function getInfoAdresse(): ?string
    {
        return $this->info_adresse;
    }

    public function setInfoAdresse(?string $info_adresse): static
    {
        $this->info_adresse = $info_adresse;

        return $this;
    }

    public function getInfoCp(): ?string
    {
        return $this->info_cp;
    }

    public function setInfoCp(?string $info_cp): static
    {
        $this->info_cp = $info_cp;

        return $this;
    }

    public function getInfoVille(): ?string
    {
        return $this->info_ville;
    }

    public function setInfoVille(?string $info_ville): static
    {
        $this->info_ville = $info_ville;

        return $this;
    }

    public function getInfoPays(): ?string
    {
        return $this->info_pays;
    }

    public function setInfoPays(?string $info_pays): static
    {
        $this->info_pays = $info_pays;

        return $this;
    }

    public function getInfoTel(): ?string
    {
        return $this->info_tel;
    }

    public function setInfoTel(?string $info_tel): static
    {
        $this->info_tel = $info_tel;

        return $this;
    }

    public function getInfoSiret(): ?string
    {
        return $this->info_siret;
    }

    public function setInfoSiret(?string $info_siret): static
    {
        $this->info_siret = $info_siret;

        return $this;
    }

    public function getInfoEmail(): ?string
    {
        return $this->info_email;
    }

    public function setInfoEmail(?string $info_email): static
    {
        $this->info_email = $info_email;

        return $this;
    }

    public function getInfoChambre(): ?string
    {
        return $this->info_chambre;
    }

    public function setInfoChambre(?string $info_chambre): static
    {
        $this->info_chambre = $info_chambre;

        return $this;
    }

    public function getDateVersion(): ?string
    {
        return $this->date_version;
    }

    public function setDateVersion(?string $date_version): static
    {
        $this->date_version = $date_version;

        return $this;
    }

    public function getDateValeurs(): ?string
    {
        return $this->date_valeurs;
    }

    public function setDateValeurs(?string $date_valeurs): static
    {
        $this->date_valeurs = $date_valeurs;

        return $this;
    }

    public function getFactureObjet(): ?string
    {
        return $this->facture_objet;
    }

    public function setFactureObjet(?string $facture_objet): static
    {
        $this->facture_objet = $facture_objet;

        return $this;
    }

    public function getFactureTitle(): ?string
    {
        return $this->facture_title;
    }

    public function setFactureTitle(?string $facture_title): static
    {
        $this->facture_title = $facture_title;

        return $this;
    }

    public function getFactureFooter(): ?string
    {
        return $this->facture_footer;
    }

    public function setFactureFooter(?string $facture_footer): static
    {
        $this->facture_footer = $facture_footer;

        return $this;
    }

    public function getPhase(): ?string
    {
        return $this->phase;
    }

    public function setPhase(?string $phase): static
    {
        $this->phase = $phase;

        return $this;
    }

    public function getPayementVersion(): ?string
    {
        return $this->payement_version;
    }

    public function setPayementVersion(?string $payement_version): static
    {
        $this->payement_version = $payement_version;

        return $this;
    }

    public function getPayementId(): ?string
    {
        return $this->payement_id;
    }

    public function setPayementId(?string $payement_id): static
    {
        $this->payement_id = $payement_id;

        return $this;
    }

    public function getPayementKey(): ?string
    {
        return $this->payement_key;
    }

    public function setPayementKey(?string $payement_key): static
    {
        $this->payement_key = $payement_key;

        return $this;
    }

    public function getPayementVad(): ?string
    {
        return $this->payement_vad;
    }

    public function setPayementVad(?string $payement_vad): static
    {
        $this->payement_vad = $payement_vad;

        return $this;
    }

    public function getPayementPk(): ?string
    {
        return $this->payement_pk;
    }

    public function setPayementPk(?string $payement_pk): static
    {
        $this->payement_pk = $payement_pk;

        return $this;
    }

    public function getPayementSk(): ?string
    {
        return $this->payement_sk;
    }

    public function setPayementSk(?string $payement_sk): static
    {
        $this->payement_sk = $payement_sk;

        return $this;
    }

    public function getPrixCasque(): ?string
    {
        return $this->prix_casque;
    }

    public function setPrixCasque(?string $prix_casque): static
    {
        $this->prix_casque = $prix_casque;

        return $this;
    }
}
