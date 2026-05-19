<?php

namespace App\Entity;

use App\Repository\FavorisRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FavorisRepository::class)]
class Favoris
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'idEtablissement', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Etablissement $Etablissement = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'idCategorie', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Categories $Categorie = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomCategorie = null;

    #[ORM\Column(nullable: true)]
    private ?int $idElement = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomElement = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtablissement(): ?Etablissement
    {
        return $this->Etablissement;
    }

    public function setEtablissement(?Etablissement $Etablissement): static
    {
        $this->Etablissement = $Etablissement;

        return $this;
    }

    public function getCategorie(): ?Categories
    {
        return $this->Categorie;
    }

    public function setCategorie(?Categories $Categorie): static
    {
        $this->Categorie = $Categorie;

        return $this;
    }

    public function getNomCategorie(): ?string
    {
        return $this->nomCategorie;
    }

    public function setNomCategorie(?string $nomCategorie): static
    {
        $this->nomCategorie = $nomCategorie;

        return $this;
    }

    public function getIdElement(): ?int
    {
        return $this->idElement;
    }

    public function setIdElement(?int $idElement): static
    {
        $this->idElement = $idElement;

        return $this;
    }

    public function getNomElement(): ?string
    {
        return $this->nomElement;
    }

    public function setNomElement(?string $nomElement): static
    {
        $this->nomElement = $nomElement;

        return $this;
    }
}
