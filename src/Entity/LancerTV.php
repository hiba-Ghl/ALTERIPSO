<?php

namespace App\Entity;

use App\Repository\LancerTVRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LancerTVRepository::class)]
class LancerTV
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $idchambre = null;

    #[ORM\Column]
    private ?int $idTV = null;

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

    public function getIdTV(): ?int
    {
        return $this->idTV;
    }

    public function setIdTV(int $idTV): static
    {
        $this->idTV = $idTV;

        return $this;
    }
}
