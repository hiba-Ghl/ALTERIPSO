<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240228100239 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chambre (id INT AUTO_INCREMENT NOT NULL, etablissement_id INT DEFAULT NULL, service_id INT DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, etat VARCHAR(255) DEFAULT NULL, ip VARCHAR(255) DEFAULT NULL, mac VARCHAR(255) DEFAULT NULL, active VARCHAR(255) DEFAULT NULL, etage VARCHAR(255) DEFAULT NULL, support VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, date VARCHAR(255) DEFAULT NULL, version VARCHAR(255) DEFAULT NULL, client VARCHAR(255) DEFAULT NULL, cin VARCHAR(255) DEFAULT NULL, cout VARCHAR(255) DEFAULT NULL, checkval VARCHAR(255) DEFAULT NULL, drois VARCHAR(255) DEFAULT NULL, langue VARCHAR(255) DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, background VARCHAR(255) DEFAULT NULL, typeaffichage VARCHAR(255) DEFAULT NULL, chaine VARCHAR(255) DEFAULT NULL, status VARCHAR(255) DEFAULT NULL, INDEX IDX_C509E4FFFF631228 (etablissement_id), INDEX IDX_C509E4FFED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE chambre ADD CONSTRAINT FK_C509E4FFFF631228 FOREIGN KEY (etablissement_id) REFERENCES etablissement (id)');
        $this->addSql('ALTER TABLE chambre ADD CONSTRAINT FK_C509E4FFED5CA9E6 FOREIGN KEY (service_id) REFERENCES service_etablissement (id)');
       // $this->addSql('ALTER TABLE etablissement CHANGE id id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chambre DROP FOREIGN KEY FK_C509E4FFFF631228');
        $this->addSql('ALTER TABLE chambre DROP FOREIGN KEY FK_C509E4FFED5CA9E6');
        $this->addSql('DROP TABLE chambre');
       // $this->addSql('ALTER TABLE etablissement CHANGE id id INT AUTO_INCREMENT NOT NULL');
    }
}
