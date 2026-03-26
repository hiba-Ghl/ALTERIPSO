<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260324090000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add rss_style JSON column on annonce for fine-grained Flux RSS styling';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE annonce ADD rss_style JSON DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE annonce DROP rss_style');
    }
}
