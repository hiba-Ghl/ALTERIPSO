<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240222110104 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE television (id INT AUTO_INCREMENT NOT NULL, etablissement_id INT DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, ip VARCHAR(255) DEFAULT NULL, port VARCHAR(255) DEFAULT NULL, protocole VARCHAR(255) DEFAULT NULL, pays VARCHAR(255) DEFAULT NULL, numero INT DEFAULT NULL, active INT DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, gratuite INT DEFAULT NULL, INDEX IDX_6BAC60C3FF631228 (etablissement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE television ADD CONSTRAINT FK_6BAC60C3FF631228 FOREIGN KEY (etablissement_id) REFERENCES etablissement (id)');
        //$this->addSql('ALTER TABLE etablissement CHANGE id id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE television DROP FOREIGN KEY FK_6BAC60C3FF631228');
        $this->addSql('DROP TABLE television');
      //  $this->addSql('ALTER TABLE etablissement CHANGE id id INT AUTO_INCREMENT NOT NULL');
    }
}
