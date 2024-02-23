<?php

namespace App\Entity;

use App\Repository\TelevisionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TelevisionRepository::class)]
class Television
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ip = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $port = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $protocole = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pays = null;

    #[ORM\Column(nullable: true)]
    private ?int $numero = null;

    #[ORM\Column(nullable: true)]
    private ?int $active = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    #[ORM\Column(nullable: true)]
    private ?int $gratuite = null;

    #[ORM\ManyToOne(inversedBy: 'televisions')]
    private ?etablissement $etablissement = null;

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

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): static
    {
        $this->ip = $ip;

        return $this;
    }

    public function getPort(): ?string
    {
        return $this->port;
    }

    public function setPort(?string $port): static
    {
        $this->port = $port;

        return $this;
    }

    public function getProtocole(): ?string
    {
        return $this->protocole;
    }

    public function setProtocole(?string $protocole): static
    {
        $this->protocole = $protocole;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(?string $pays): static
    {
        $this->pays = $pays;

        return $this;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(?int $numero): static
    {
        $this->numero = $numero;

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

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    public function getGratuite(): ?int
    {
        return $this->gratuite;
    }

    public function setGratuite(?int $gratuite): static
    {
        $this->gratuite = $gratuite;

        return $this;
    }

    public function getEtablissement(): ?etablissement
    {
        return $this->etablissement;
    }

    public function setEtablissement(?etablissement $etablissement): static
    {
        $this->etablissement = $etablissement;

        return $this;
    }
}
