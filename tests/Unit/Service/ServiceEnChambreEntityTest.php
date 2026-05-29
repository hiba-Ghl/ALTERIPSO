<?php

namespace App\Tests\Unit\Service;

use App\Entity\ServiceEnChambre;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for the service-in-room entity used by the back-office.
 */
class ServiceEnChambreEntityTest extends TestCase
{
    public function testItStoresTranslationsAndContent(): void
    {
        $service = new ServiceEnChambre();

        $service->setNom('Room service');
        $service->setPosition(2);
        $service->setActive(true);
        $service->setContenu('{"items":[]}');
        $service->setLogo('logo.png');
        $service->setDescription('Description de test');
        $service->setFr('Service de chambre');
        $service->setEn('Room service');

        self::assertSame('Room service', $service->getNom());
        self::assertSame(2, $service->getPosition());
        self::assertTrue($service->isActive());
        self::assertSame('{"items":[]}', $service->getContenu());
        self::assertSame('logo.png', $service->getLogo());
        self::assertSame('Description de test', $service->getDescription());
    }
}