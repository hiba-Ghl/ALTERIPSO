<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240903144334 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE annonce (id INT AUTO_INCREMENT NOT NULL, etablissement_id INT DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, url LONGTEXT DEFAULT NULL, datedebut VARCHAR(255) DEFAULT NULL, datefin VARCHAR(255) DEFAULT NULL, duree INT DEFAULT NULL, theme VARCHAR(255) DEFAULT NULL, position VARCHAR(255) DEFAULT NULL, fr LONGTEXT DEFAULT NULL, en LONGTEXT DEFAULT NULL, es LONGTEXT DEFAULT NULL, pt LONGTEXT DEFAULT NULL, it LONGTEXT DEFAULT NULL, ru LONGTEXT DEFAULT NULL, de LONGTEXT DEFAULT NULL, zh LONGTEXT DEFAULT NULL, ar LONGTEXT DEFAULT NULL, police VARCHAR(255) DEFAULT NULL, taille INT DEFAULT NULL, style VARCHAR(255) DEFAULT NULL, INDEX IDX_F65593E5FF631228 (etablissement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categorie_vod (id INT AUTO_INCREMENT NOT NULL, etablissement_id INT DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, position INT NOT NULL, logo VARCHAR(255) DEFAULT NULL, active VARCHAR(255) DEFAULT NULL, fr VARCHAR(255) DEFAULT NULL, en VARCHAR(255) DEFAULT NULL, es VARCHAR(255) DEFAULT NULL, pt VARCHAR(255) DEFAULT NULL, it VARCHAR(255) DEFAULT NULL, ru VARCHAR(255) DEFAULT NULL, de VARCHAR(255) DEFAULT NULL, zh VARCHAR(255) DEFAULT NULL, ar VARCHAR(255) DEFAULT NULL, INDEX IDX_2930DED8FF631228 (etablissement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE historique_annonce (id INT AUTO_INCREMENT NOT NULL, etablissement_id INT DEFAULT NULL, chambre_id INT DEFAULT NULL, annonce_id INT DEFAULT NULL, dtenvoie VARCHAR(255) DEFAULT NULL, INDEX IDX_9441CC9AFF631228 (etablissement_id), INDEX IDX_9441CC9A9B177F54 (chambre_id), INDEX IDX_9441CC9A8805AB2F (annonce_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vod (id INT AUTO_INCREMENT NOT NULL, etablissement_id INT DEFAULT NULL, categorie_id INT DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, active VARCHAR(255) DEFAULT NULL, INDEX IDX_7871FD9FF631228 (etablissement_id), INDEX IDX_7871FD9BCF5E72D (categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE annonce ADD CONSTRAINT FK_F65593E5FF631228 FOREIGN KEY (etablissement_id) REFERENCES etablissement (id)');
        $this->addSql('ALTER TABLE categorie_vod ADD CONSTRAINT FK_2930DED8FF631228 FOREIGN KEY (etablissement_id) REFERENCES etablissement (id)');
        $this->addSql('ALTER TABLE historique_annonce ADD CONSTRAINT FK_9441CC9AFF631228 FOREIGN KEY (etablissement_id) REFERENCES etablissement (id)');
        $this->addSql('ALTER TABLE historique_annonce ADD CONSTRAINT FK_9441CC9A9B177F54 FOREIGN KEY (chambre_id) REFERENCES chambre (id)');
        $this->addSql('ALTER TABLE historique_annonce ADD CONSTRAINT FK_9441CC9A8805AB2F FOREIGN KEY (annonce_id) REFERENCES annonce (id)');
        $this->addSql('ALTER TABLE vod ADD CONSTRAINT FK_7871FD9FF631228 FOREIGN KEY (etablissement_id) REFERENCES etablissement (id)');
        $this->addSql('ALTER TABLE vod ADD CONSTRAINT FK_7871FD9BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie_vod (id)');
      //  $this->addSql('ALTER TABLE etablissement CHANGE id id INT NOT NULL');
      //  $this->addSql('ALTER TABLE service_en_chambre CHANGE contenu contenu VARCHAR(20000) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce DROP FOREIGN KEY FK_F65593E5FF631228');
        $this->addSql('ALTER TABLE categorie_vod DROP FOREIGN KEY FK_2930DED8FF631228');
        $this->addSql('ALTER TABLE historique_annonce DROP FOREIGN KEY FK_9441CC9AFF631228');
        $this->addSql('ALTER TABLE historique_annonce DROP FOREIGN KEY FK_9441CC9A9B177F54');
        $this->addSql('ALTER TABLE historique_annonce DROP FOREIGN KEY FK_9441CC9A8805AB2F');
        $this->addSql('ALTER TABLE vod DROP FOREIGN KEY FK_7871FD9FF631228');
        $this->addSql('ALTER TABLE vod DROP FOREIGN KEY FK_7871FD9BCF5E72D');
        $this->addSql('DROP TABLE annonce');
        $this->addSql('DROP TABLE categorie_vod');
        $this->addSql('DROP TABLE historique_annonce');
        $this->addSql('DROP TABLE vod');
      //  $this->addSql('ALTER TABLE etablissement CHANGE id id INT AUTO_INCREMENT NOT NULL');
       // $this->addSql('ALTER TABLE service_en_chambre CHANGE contenu contenu MEDIUMTEXT NOT NULL');
    }
}
