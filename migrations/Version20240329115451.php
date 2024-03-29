<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240329115451 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
      /*  $this->addSql('CREATE TABLE service_en_chambre (id INT AUTO_INCREMENT NOT NULL, type_service_en_chambre_id INT DEFAULT NULL, etablissement_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, position INT DEFAULT NULL, active TINYINT(1) DEFAULT NULL, contenu VARCHAR(20000) NOT NULL, logo VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, fr VARCHAR(255) DEFAULT NULL, en VARCHAR(255) DEFAULT NULL, es VARCHAR(255) DEFAULT NULL, pt VARCHAR(255) DEFAULT NULL, it VARCHAR(255) DEFAULT NULL, ru VARCHAR(255) DEFAULT NULL, de VARCHAR(255) DEFAULT NULL, zh VARCHAR(255) DEFAULT NULL, ar VARCHAR(255) DEFAULT NULL, INDEX IDX_2D71E2BE558D03F (type_service_en_chambre_id), INDEX IDX_2D71E2BFF631228 (etablissement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_service_en_chambre (id INT AUTO_INCREMENT NOT NULL, etablissement_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_65B665FBFF631228 (etablissement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE service_en_chambre ADD CONSTRAINT FK_2D71E2BE558D03F FOREIGN KEY (type_service_en_chambre_id) REFERENCES type_service_en_chambre (id)');
        $this->addSql('ALTER TABLE service_en_chambre ADD CONSTRAINT FK_2D71E2BFF631228 FOREIGN KEY (etablissement_id) REFERENCES etablissement (id)');
        $this->addSql('ALTER TABLE type_service_en_chambre ADD CONSTRAINT FK_65B665FBFF631228 FOREIGN KEY (etablissement_id) REFERENCES etablissement (id)');
        $this->addSql('ALTER TABLE etablissement CHANGE id id INT NOT NULL');*/
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
      /*  $this->addSql('ALTER TABLE service_en_chambre DROP FOREIGN KEY FK_2D71E2BE558D03F');
        $this->addSql('ALTER TABLE service_en_chambre DROP FOREIGN KEY FK_2D71E2BFF631228');
        $this->addSql('ALTER TABLE type_service_en_chambre DROP FOREIGN KEY FK_65B665FBFF631228');
        $this->addSql('DROP TABLE service_en_chambre');
        $this->addSql('DROP TABLE type_service_en_chambre');
        $this->addSql('ALTER TABLE etablissement CHANGE id id INT AUTO_INCREMENT NOT NULL');*/
    }
}
