<?php

namespace App\Tests\Unit\Annonce;

use App\Entity\Annonce;
use App\Entity\HistoriqueAnnonce;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for advertisement accessors and relation helpers.
 */
class AnnonceEntityTest extends TestCase
{
    public function testItKeepsTheCoreDisplaySettings(): void
    {
        $annonce = new Annonce();

        $annonce->setNom('Annonce back-office');
        $annonce->setType('Message');
        $annonce->setPosition('Haut');
        $annonce->setDuree(30);
        $annonce->setActive(true);

        self::assertSame('Annonce back-office', $annonce->getNom());
        self::assertSame('Message', $annonce->getType());
        self::assertSame('Haut', $annonce->getPosition());
        self::assertSame(30, $annonce->getDuree());
        self::assertTrue($annonce->isActive());
    }

    public function testItAddsAndRemovesHistoricEntries(): void
    {
        $annonce = new Annonce();
        $historique = new HistoriqueAnnonce();

        $annonce->addHistoriqueAnnonce($historique);

        self::assertCount(1, $annonce->getHistoriqueAnnonces());
        self::assertSame($annonce, $historique->getAnnonce());

        $annonce->removeHistoriqueAnnonce($historique);

        self::assertCount(0, $annonce->getHistoriqueAnnonces());
    }
}