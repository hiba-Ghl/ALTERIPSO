<?php

namespace App\Entity;

use App\Repository\TypeServiceEnChambreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeServiceEnChambreRepository::class)]
class TypeServiceEnChambre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'type_service_en_chambre', targetEntity: ServiceEnChambre::class)]
    private Collection $serviceEnChambres;

    #[ORM\ManyToOne(inversedBy: 'typeServiceEnChambres')]
    private ?Etablissement $etablissement = null;

    public function __construct()
    {
        $this->serviceEnChambres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, ServiceEnChambre>
     */
    public function getServiceEnChambres(): Collection
    {
        return $this->serviceEnChambres;
    }

    public function addServiceEnChambre(ServiceEnChambre $serviceEnChambre): static
    {
        if (!$this->serviceEnChambres->contains($serviceEnChambre)) {
            $this->serviceEnChambres->add($serviceEnChambre);
            $serviceEnChambre->setTypeServiceEnChambre($this);
        }

        return $this;
    }

    public function removeServiceEnChambre(ServiceEnChambre $serviceEnChambre): static
    {
        if ($this->serviceEnChambres->removeElement($serviceEnChambre)) {
            // set the owning side to null (unless already changed)
            if ($serviceEnChambre->getTypeServiceEnChambre() === $this) {
                $serviceEnChambre->setTypeServiceEnChambre(null);
            }
        }

        return $this;
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
}
