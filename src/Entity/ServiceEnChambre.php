<?php

namespace App\Entity;

use App\Repository\ServiceEnChambreRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServiceEnChambreRepository::class)]
class ServiceEnChambre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(nullable: true)]
    private ?int $position = null;

    #[ORM\Column(nullable: true)]
    private ?bool $active = null;

    #[ORM\Column(length: 20000,nullable: false)]
    private ?string $contenu = null;

    #[ORM\Column(length: 255)]
    private ?string $logo = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'serviceEnChambres')]
    private ?TypeServiceEnChambre $type_service_en_chambre = null;

    #[ORM\ManyToOne(inversedBy: 'serviceEnChambre')]
    private ?Etablissement $etablissement = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $en = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $es = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $it = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ru = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $de = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $zh = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ar = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): static
    {
        $this->logo = $logo;

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

    public function getTypeServiceEnChambre(): ?TypeServiceEnChambre
    {
        return $this->type_service_en_chambre;
    }

    public function setTypeServiceEnChambre(?TypeServiceEnChambre $type_service_en_chambre): static
    {
        $this->type_service_en_chambre = $type_service_en_chambre;

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

    public function getFr(): ?string
    {
        return $this->fr;
    }

    public function setFr(?string $fr): static
    {
        $this->fr = $fr;

        return $this;
    }

    public function getEn(): ?string
    {
        return $this->en;
    }

    public function setEn(?string $en): static
    {
        $this->en = $en;

        return $this;
    }

    public function getEs(): ?string
    {
        return $this->es;
    }

    public function setEs(?string $es): static
    {
        $this->es = $es;

        return $this;
    }

    public function getPt(): ?string
    {
        return $this->pt;
    }

    public function setPt(?string $pt): static
    {
        $this->pt = $pt;

        return $this;
    }

    public function getIt(): ?string
    {
        return $this->it;
    }

    public function setIt(?string $it): static
    {
        $this->it = $it;

        return $this;
    }

    public function getRu(): ?string
    {
        return $this->ru;
    }

    public function setRu(?string $ru): static
    {
        $this->ru = $ru;

        return $this;
    }

    public function getDe(): ?string
    {
        return $this->de;
    }

    public function setDe(?string $de): static
    {
        $this->de = $de;

        return $this;
    }

    public function getZh(): ?string
    {
        return $this->zh;
    }

    public function setZh(?string $zh): static
    {
        $this->zh = $zh;

        return $this;
    }

    public function getAr(): ?string
    {
        return $this->ar;
    }

    public function setAr(?string $ar): static
    {
        $this->ar = $ar;

        return $this;
    }
}
