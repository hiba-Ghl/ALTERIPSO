<?php

namespace App\Entity;

use App\Repository\LancerAnnonceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LancerAnnonceRepository::class)]
class LancerAnnonce
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $idchambre = null;

    #[ORM\Column]
    private ?int $idAnnonce = null;

     #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateEnvoie = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdchambre(): ?int
    {
        return $this->idchambre;
    }

    public function setIdchambre(int $idchambre): static
    {
        $this->idchambre = $idchambre;

        return $this;
    }

    public function getIdAnnonce(): ?int
    {
        return $this->idAnnonce;
    }

    public function setIdAnnonce(int $idAnnonce): static
    {
        $this->idAnnonce = $idAnnonce;

        return $this;
    }
    public function setDateEnvoie(\DateTimeInterface $dateEnvoie): static
    {
        $this->dateEnvoie = $dateEnvoie;

        return $this;
    }
    public function getDateEnvoie(): ?\DateTimeInterface
    {
        return $this->dateEnvoie;
    }
}
