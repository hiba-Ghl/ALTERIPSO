<?php

namespace App\Entity;

use App\Repository\AnnonceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnnonceRepository::class)]
class Annonce
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'annonces')]
    private ?Etablissement $etablissement = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $type = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $url = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $datedebut = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $datefin = null;

    #[ORM\Column(nullable: true)]
    private ?int $duree = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $theme = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $position = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $fr = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $en = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $es = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $pt = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $it = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $ru = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $de = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $zh = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $ar = null;

    #[ORM\OneToMany(mappedBy: 'annonce', targetEntity: HistoriqueAnnonce::class)]
    private Collection $historiqueAnnonces;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $police = null;

    #[ORM\Column(nullable: true)]
    private ?int $taille = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $style = null;

    public function __construct()
    {
        $this->historiqueAnnonces = new ArrayCollection();
    }

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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getDatedebut(): ?string
    {
        return $this->datedebut;
    }

    public function setDatedebut(?string $datedebut): static
    {
        $this->datedebut = $datedebut;

        return $this;
    }

    public function getDatefin(): ?string
    {
        return $this->datefin;
    }

    public function setDatefin(?string $datefin): static
    {
        $this->datefin = $datefin;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function setTheme(?string $theme): static
    {
        $this->theme = $theme;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(?string $position): static
    {
        $this->position = $position;

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

    /**
     * @return Collection<int, HistoriqueAnnonce>
     */
    public function getHistoriqueAnnonces(): Collection
    {
        return $this->historiqueAnnonces;
    }

    public function addHistoriqueAnnonce(HistoriqueAnnonce $historiqueAnnonce): static
    {
        if (!$this->historiqueAnnonces->contains($historiqueAnnonce)) {
            $this->historiqueAnnonces->add($historiqueAnnonce);
            $historiqueAnnonce->setAnnonce($this);
        }

        return $this;
    }

    public function removeHistoriqueAnnonce(HistoriqueAnnonce $historiqueAnnonce): static
    {
        if ($this->historiqueAnnonces->removeElement($historiqueAnnonce)) {
            // set the owning side to null (unless already changed)
            if ($historiqueAnnonce->getAnnonce() === $this) {
                $historiqueAnnonce->setAnnonce(null);
            }
        }

        return $this;
    }

    public function getPolice(): ?string
    {
        return $this->police;
    }

    public function setPolice(?string $police): static
    {
        $this->police = $police;

        return $this;
    }

    public function getTaille(): ?int
    {
        return $this->taille;
    }

    public function setTaille(?int $taille): static
    {
        $this->taille = $taille;

        return $this;
    }

    public function getStyle(): ?string
    {
        return $this->style;
    }

    public function setStyle(?string $style): static
    {
        $this->style = $style;

        return $this;
    }
}
