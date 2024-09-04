<?php

namespace App\Entity;

use App\Repository\HistoriqueAnnonceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoriqueAnnonceRepository::class)]
class HistoriqueAnnonce
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'historiqueAnnonces')]
    private ?Etablissement $etablissement = null;

    #[ORM\ManyToOne(inversedBy: 'historiqueAnnonces')]
    private ?Chambre $chambre = null;

    #[ORM\ManyToOne(inversedBy: 'historiqueAnnonces')]
    private ?Annonce $annonce = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $dtenvoie = null;

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

    public function getChambre(): ?Chambre
    {
        return $this->chambre;
    }

    public function setChambre(?Chambre $chambre): static
    {
        $this->chambre = $chambre;

        return $this;
    }

    public function getAnnonce(): ?Annonce
    {
        return $this->annonce;
    }

    public function setAnnonce(?Annonce $annonce): static
    {
        $this->annonce = $annonce;

        return $this;
    }

    public function getDtenvoie(): ?string
    {
        return $this->dtenvoie;
    }

    public function setDtenvoie(?string $dtenvoie): static
    {
        $this->dtenvoie = $dtenvoie;

        return $this;
    }
}
