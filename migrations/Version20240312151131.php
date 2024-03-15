<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240312151131 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE etablissement DROP FOREIGN KEY FK_20FD592C315B405');
        $this->addSql('DROP INDEX IDX_20FD592C315B405 ON etablissement');
        $this->addSql('ALTER TABLE etablissement DROP support_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE etablissement ADD support_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE etablissement ADD CONSTRAINT FK_20FD592C315B405 FOREIGN KEY (support_id) REFERENCES support (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_20FD592C315B405 ON etablissement (support_id)');
    }
}
