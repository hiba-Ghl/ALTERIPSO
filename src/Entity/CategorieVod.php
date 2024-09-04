<?php

namespace App\Entity;

use App\Repository\CategorieVodRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorieVodRepository::class)]
class CategorieVod
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?int $position = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $active = null;

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

    #[ORM\ManyToOne(inversedBy: 'categorieVods')]
    private ?Etablissement $etablissement = null;

    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: Vod::class)]
    private Collection $vods;

    public function __construct()
    {
        $this->vods = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

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

    public function getActive(): ?string
    {
        return $this->active;
    }

    public function setActive(?string $active): static
    {
        $this->active = $active;

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

    /**
     * @return Collection<int, Vod>
     */
    public function getVods(): Collection
    {
        return $this->vods;
    }

    public function addVod(Vod $vod): static
    {
        if (!$this->vods->contains($vod)) {
            $this->vods->add($vod);
            $vod->setCategorie($this);
        }

        return $this;
    }

    public function removeVod(Vod $vod): static
    {
        if ($this->vods->removeElement($vod)) {
            // set the owning side to null (unless already changed)
            if ($vod->getCategorie() === $this) {
                $vod->setCategorie(null);
            }
        }

        return $this;
    }
}
