<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240228092810 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE service_etablissement (id INT AUTO_INCREMENT NOT NULL, etablissement_id INT DEFAULT NULL, active VARCHAR(255) DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, background VARCHAR(255) DEFAULT NULL, INDEX IDX_F042E3AEFF631228 (etablissement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE service_etablissement ADD CONSTRAINT FK_F042E3AEFF631228 FOREIGN KEY (etablissement_id) REFERENCES etablissement (id)');
      //  $this->addSql('ALTER TABLE etablissement CHANGE id id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service_etablissement DROP FOREIGN KEY FK_F042E3AEFF631228');
        $this->addSql('DROP TABLE service_etablissement');
      //  $this->addSql('ALTER TABLE etablissement CHANGE id id INT AUTO_INCREMENT NOT NULL');
    }
}
