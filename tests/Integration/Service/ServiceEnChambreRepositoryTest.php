<?php

namespace App\Tests\Integration\Service;

use App\Entity\ServiceEnChambre;
use App\Tests\Support\KernelDatabaseTestCase;

/**
 * Integration test for service-in-room persistence and Doctrine lookups.
 */
class ServiceEnChambreRepositoryTest extends KernelDatabaseTestCase
{
    public function testItPersistsAndOrdersServicesForTheEtablissement(): void
    {
        $etablissement = $this->createEtablissement();
        $typeService = $this->createTypeServiceEnChambre($etablissement, 'Menu');

        $serviceA = new ServiceEnChambre();
        $serviceA->setNom('Service A');
        $serviceA->setPosition(2);
        $serviceA->setActive(true);
        $serviceA->setContenu('[]');
        $serviceA->setLogo('service-a.png');
        $serviceA->setDescription('Premier service');
        $serviceA->setTypeServiceEnChambre($typeService);
        $serviceA->setEtablissement($etablissement);

        $serviceB = new ServiceEnChambre();
        $serviceB->setNom('Service B');
        $serviceB->setPosition(1);
        $serviceB->setActive(true);
        $serviceB->setContenu('[]');
        $serviceB->setLogo('service-b.png');
        $serviceB->setDescription('Deuxième service');
        $serviceB->setTypeServiceEnChambre($typeService);
        $serviceB->setEtablissement($etablissement);

        $this->persist($serviceA);
        $this->persist($serviceB);

        $this->entityManager->clear();

        $reloadedServices = $this->entityManager
            ->getRepository(ServiceEnChambre::class)
            ->findBy(['etablissement' => $etablissement], ['position' => 'ASC']);

        self::assertCount(2, $reloadedServices);
        self::assertSame('Service B', $reloadedServices[0]->getNom());
        self::assertSame('Service A', $reloadedServices[1]->getNom());
    }
}