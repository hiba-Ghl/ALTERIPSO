<?php

namespace App\Entity;

use App\Repository\HistoriquegratuiteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoriquegratuiteRepository::class)]
class Historiquegratuite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $date = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $datein = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $dateout = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $type = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?etablissement $etablissement = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(?string $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getDatein(): ?string
    {
        return $this->datein;
    }

    public function setDatein(?string $datein): static
    {
        $this->datein = $datein;

        return $this;
    }

    public function getDateout(): ?string
    {
        return $this->dateout;
    }

    public function setDateout(?string $dateout): static
    {
        $this->dateout = $dateout;

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
