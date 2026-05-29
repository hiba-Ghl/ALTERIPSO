<?php

namespace App\Tests\Integration\Annonce;

use App\Entity\Annonce;
use App\Tests\Support\KernelDatabaseTestCase;

/**
 * Integration test for advertisement persistence and Doctrine filters.
 */
class AnnonceRepositoryTest extends KernelDatabaseTestCase
{
    public function testItPersistsAndReloadsAnAnnonceForTheEtablissement(): void
    {
        $etablissement = $this->createEtablissement();
        $createdAnnonce = $this->createAnnonce($etablissement, [
            'nom' => 'Promotion spa',
            'type' => 'Message',
            'url' => 'Bienvenue au spa',
            'position' => 'Bas',
        ]);

        $this->entityManager->clear();

        $repository = $this->entityManager->getRepository(Annonce::class);
        $results = $repository->findBy(['etablissement' => $etablissement], ['nom' => 'ASC']);

        self::assertCount(1, $results);
        self::assertSame($createdAnnonce->getId(), $results[0]->getId());
        self::assertSame('Promotion spa', $results[0]->getNom());
    }
}