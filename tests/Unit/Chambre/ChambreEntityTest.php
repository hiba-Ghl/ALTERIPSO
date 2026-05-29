<?php

namespace App\Tests\Unit\Chambre;

use App\Entity\Chambre;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for Chambre entity accessors and defaults.
 */
class ChambreEntityTest extends TestCase
{
    public function testItStoresCoreRoomData(): void
    {
        $chambre = new Chambre();

        $chambre->setNom('CH-101');
        $chambre->setIp('192.168.0.101');
        $chambre->setMac('AA:BB:CC:DD:EE:FF');
        $chambre->setActive('1');

        self::assertSame('CH-101', $chambre->getNom());
        self::assertSame('192.168.0.101', $chambre->getIp());
        self::assertSame('AA:BB:CC:DD:EE:FF', $chambre->getMac());
        self::assertSame('1', $chambre->getActive());
    }
}