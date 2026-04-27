<?php

namespace App\Entity;

use App\Repository\CategoriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: CategoriesRepository::class)]
class Categories
{
    #[ORM\Id]
    // #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $titre = null;

    #[ORM\Column(nullable: true)]
    private ?int $active = null;

    #[ORM\Column(nullable: true)]
    private ?int $position = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $html = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $package = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fr = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $en = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $es = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true)]
    private ?bool $favori = false;

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

    #[ORM\ManyToOne(inversedBy: 'categories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etablissement $etablissement = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $background = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $typemenu = null;

    /**
     * @var Collection<int, Services>
     */
    #[ORM\OneToMany(mappedBy: 'categories', targetEntity: Services::class)]
    private Collection $services;

    public function __construct()
    {
        $this->services = new ArrayCollection();
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

    public function isFavori(): ?bool
    {
        return $this->favori;
    }

    public function setFavori(?bool $favori): static
    {
        $this->favori = $favori;

        return $this;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }
    

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getActive(): ?int
    {
        return $this->active;
    }

    public function setActive(?int $active): static
    {
        $this->active = $active;

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

    public function getHtml(): ?string
    {
        return $this->html;
    }

    public function setHtml(?string $html): static
    {
        $this->html = $html;

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

    public function getPackage(): ?string
    {
        return $this->package;
    }

    public function setPackage(?string $package): static
    {
        $this->package = $package;

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

    public function getEtablissement(): ?Etablissement
    {
        return $this->etablissement;
    }

    public function setEtablissement(?Etablissement $etablissement): static
    {
        $this->etablissement = $etablissement;

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

    public function getTypemenu(): ?string
    {
        return $this->typemenu;
    }

    public function setTypemenu(?string $typemenu): static
    {
        $this->typemenu = $typemenu;

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
            $Services->setCategories($this);
        }

        return $this;
    }

    public function removeServices(Services $Services): static
    {
        if ($this->services->removeElement($Services)) {
            if ($Services->getCategories() === $this) {
                $Services->setCategories(null);
            }
        }
        return $this;
    }


  
}