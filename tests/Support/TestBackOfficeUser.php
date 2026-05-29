<?php

namespace App\Tests\Support;

use App\Entity\Etablissement;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Lightweight authenticated user used by functional tests.
 */
final class TestBackOfficeUser implements UserInterface
{
    private array $flags;

    public function __construct(
        private readonly string $identifier,
        private readonly Etablissement $etablissement,
        array $flags = [],
        private readonly array $roles = ['ROLE_USER']
    ) {
        $this->flags = $flags + [
            'getQUESTIONNAIRE' => true,
            'getANNONCES' => true,
            'getSERVICE' => true,
            'isServiceEnChambre' => true,
            'isAjoutServiceEnChambre' => true,
            'getAjouteqs' => true,
            'getModifierqs' => true,
            'getSupprimerqs' => true,
            'getSauvgarderqs' => true,
            'getAjouteSupport' => true,
            'getSupprimerSupport' => true,
            'getRedimarerSupport' => true,
            'getAjouterAnnonce' => true,
            'getModifierAnnonce' => true,
            'getSuppAnnonce' => true,
            'getSauvegarderAnnonce' => true,
            'getAjouteService' => true,
            'getModifierService' => true,
            'getSupprimerService' => true,
            'getSauvegarderService' => true,
            'getSupportConnect' => true,
            'getAjoutServiceEnChambre' => true,
            'getModifierServiceEnChambre' => true,
            'getSuppServiceEnChambre' => true,
            'getSauvegarderServiceEnChambre' => true,
            'getAjouteServiceEtablissement' => true,
            'getCheckSupport' => true,
        ];
    }

    public function getUserIdentifier(): string
    {
        return $this->identifier;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function eraseCredentials(): void
    {
    }

    public function getEtablissement(): Etablissement
    {
        return $this->etablissement;
    }

    public function __call(string $name, array $arguments): mixed
    {
        return $this->flags[$name] ?? false;
    }
}