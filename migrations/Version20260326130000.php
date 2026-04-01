<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260326130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout de la colonne vitesse_defilement à la table annonce';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE annonce ADD vitesse_defilement INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE annonce DROP vitesse_defilement');
    }
}
