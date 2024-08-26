<?php

namespace App\Entity;

use App\Repository\ResultatQuestionnaireRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResultatQuestionnaireRepository::class)]
class ResultatQuestionnaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'resultatQuestionnaires')]
    private ?Etablissement $etablissement = null;

    #[ORM\ManyToOne(inversedBy: 'resultatQuestionnaires')]
    private ?Questionnaire $questionnaire = null;

    #[ORM\ManyToOne(inversedBy: 'resultatQuestionnaires')]
    private ?Chambre $chambre = null;

    #[ORM\ManyToOne(inversedBy: 'resultatQuestionnaires')]
    private ?ServiceEtablissement $service = null;

    #[ORM\Column]
    private ?int $vote = null;

    #[ORM\Column(length: 255)]
    private ?string $date = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getQuestionnaire(): ?Questionnaire
    {
        return $this->questionnaire;
    }

    public function setQuestionnaire(?Questionnaire $questionnaire): static
    {
        $this->questionnaire = $questionnaire;

        return $this;
    }

    public function getChambre(): ?Chambre
    {
        return $this->chambre;
    }

    public function setChambre(?Chambre $chambre): static
    {
        $this->chambre = $chambre;

        return $this;
    }

    public function getService(): ?ServiceEtablissement
    {
        return $this->service;
    }

    public function setService(?ServiceEtablissement $service): static
    {
        $this->service = $service;

        return $this;
    }

    public function getVote(): ?int
    {
        return $this->vote;
    }

    public function setVote(int $vote): static
    {
        $this->vote = $vote;

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): static
    {
        $this->date = $date;

        return $this;
    }
}
