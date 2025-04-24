<?php

namespace App\Entity;

use App\Repository\LancerAnnonceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LancerAnnonceRepository::class)]
class LancerAnnonce
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $idChembre = null;

    #[ORM\Column]
    private ?int $idAnnonce = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdChembre(): ?int
    {
        return $this->idChembre;
    }

    public function setIdChembre(int $idChembre): static
    {
        $this->idChembre = $idChembre;

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
}
