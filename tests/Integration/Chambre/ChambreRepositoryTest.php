<?php

namespace App\Tests\Integration\Chambre;

use App\Entity\Chambre;
use App\Tests\Support\KernelDatabaseTestCase;

/**
 * Integration test for Chambre persistence and repository lookup.
 */
class ChambreRepositoryTest extends KernelDatabaseTestCase
{
    public function testItPersistsAndReloadsARoomForTheEtablissement(): void
    {
        $etablissement = $this->createEtablissement();
        $service = $this->createServiceEtablissement($etablissement);

        $createdChambre = $this->createChambre($etablissement, $service, [
            'nom' => 'CH-201',
            'ip' => '192.168.0.201',
        ]);

        $this->entityManager->clear();

        $repository = $this->entityManager->getRepository(Chambre::class);
        $reloadedChambre = $repository->find($createdChambre->getId());

        self::assertNotNull($reloadedChambre);
        self::assertSame('CH-201', $reloadedChambre->getNom());
        self::assertSame('192.168.0.201', $reloadedChambre->getIp());
        self::assertCount(1, $repository->findBy(['etablissement' => $etablissement]));
    }
}