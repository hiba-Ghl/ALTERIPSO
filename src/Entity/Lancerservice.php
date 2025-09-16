<?php

namespace App\Entity;

use App\Repository\LancerserviceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LancerserviceRepository::class)]
class Lancerservice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $idchambre = null;

    #[ORM\Column]
    private ?int $idService = null;

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

    public function getIdService(): ?int
    {
        return $this->idService;
    }

    public function setIdService(int $idService): static
    {
        $this->idService = $idService;

        return $this;
    }
}
