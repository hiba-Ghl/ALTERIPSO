<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240715101802 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
    /*    $this->addSql('CREATE TABLE services (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) DEFAULT NULL, position INT DEFAULT NULL, active VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, src VARCHAR(255) DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, service INT DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE etablissement ADD services_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE etablissement ADD CONSTRAINT FK_20FD592CAEF5A6C1 FOREIGN KEY (services_id) REFERENCES services (id)');
        $this->addSql('CREATE INDEX IDX_20FD592CAEF5A6C1 ON etablissement (services_id)');
        $this->addSql('ALTER TABLE service_en_chambre CHANGE contenu contenu VARCHAR(20000) NOT NULL');*/
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    /*    $this->addSql('ALTER TABLE etablissement DROP FOREIGN KEY FK_20FD592CAEF5A6C1');
        $this->addSql('DROP TABLE services');
        $this->addSql('DROP INDEX IDX_20FD592CAEF5A6C1 ON etablissement');
        $this->addSql('ALTER TABLE etablissement DROP services_id');
        $this->addSql('ALTER TABLE service_en_chambre CHANGE contenu contenu MEDIUMTEXT NOT NULL');*/
    }
}
