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

    #[ORM\ManyToOne(inversedBy: 'annonces', targetEntity: Etablissement::class)]
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

    

    #[ORM\OneToMany(mappedBy: 'annonce', targetEntity: HistoriqueAnnonce::class)]
    private Collection $historiqueAnnonces;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $police = null;

    #[ORM\Column(nullable: true)]
    private ?int $taille = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $style = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $animation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $esMessage = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ptMessage = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $itMessage = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ruMessage = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $deMessage = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $zhMessage = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $arMessage = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $frMessage = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $enMessage = null;



    #[ORM\Column(name: 'vitesse_defilement', type: 'integer', nullable: true)]
    private ?int $vitesseDefilement = null;
    public function getVitesseDefilement(): ?int
    {
        return $this->vitesseDefilement;
    }

    public function setVitesseDefilement(?int $vitesseDefilement): static
    {
        $this->vitesseDefilement = $vitesseDefilement;
        return $this;
    }
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


    public function getAnimation(): ?string
    {
        return $this->animation;
    }

    public function setAnimation(?string $animation): static
    {
        $this->animation = $animation;

        return $this;
    }


    public function getEsMessage(): ?string
    {
        return $this->esMessage;
    }

    public function setEsMessage(?string $esMessage): static
    {
        $this->esMessage = $esMessage;

        return $this;
    }

    public function getPtMessage(): ?string
    {
        return $this->ptMessage;
    }

    public function setPtMessage(?string $ptMessage): static
    {
        $this->ptMessage = $ptMessage;

        return $this;
    }

    public function getItMessage(): ?string
    {
        return $this->itMessage;
    }

    public function setItMessage(?string $itMessage): static
    {
        $this->itMessage = $itMessage;

        return $this;
    }

    public function getRuMessage(): ?string
    {
        return $this->ruMessage;
    }

    public function setRuMessage(?string $ruMessage): static
    {
        $this->ruMessage = $ruMessage;

        return $this;
    }

    public function getDeMessage(): ?string
    {
        return $this->deMessage;
    }

    public function setDeMessage(?string $deMessage): static
    {
        $this->deMessage = $deMessage;

        return $this;
    }

    public function getZhMessage(): ?string
    {
        return $this->zhMessage;
    }

    public function setZhMessage(?string $zhMessage): static
    {
        $this->zhMessage = $zhMessage;

        return $this;
    }

    public function getArMessage(): ?string
    {
        return $this->arMessage;
    }

    public function setArMessage(?string $arMessage): static
    {
        $this->arMessage = $arMessage;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getFrMessage(): ?string
    {
        return $this->frMessage;
    }

    public function setFrMessage(?string $frMessage): static
    {
        $this->frMessage = $frMessage;

        return $this;
    }

    public function getEnMessage(): ?string
    {
        return $this->enMessage;
    }

    public function setEnMessage(?string $enMessage): static
    {
        $this->enMessage = $enMessage;

        return $this;
    }
}