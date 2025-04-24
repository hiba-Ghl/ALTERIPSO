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
    private ?int $idChembre = null;

    #[ORM\Column]
    private ?int $idTV = null;

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
