<?php

namespace App\Entity;

use App\Repository\LancerRadioRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LancerRadioRepository::class)]
class LancerRadio
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $idChembre = null;

    #[ORM\Column]
    private ?int $idRadio = null;

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

    public function getIdRadio(): ?int
    {
        return $this->idRadio;
    }

    public function setIdRadio(int $idRadio): static
    {
        $this->idRadio = $idRadio;

        return $this;
    }
}
