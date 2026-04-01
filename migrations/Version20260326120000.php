<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260326120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add animation field to annonce';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE annonce ADD animation VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE annonce DROP animation');
    }
}
