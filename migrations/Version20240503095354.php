<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240503095354 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE configmobile (id INT AUTO_INCREMENT NOT NULL, etablissement_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, img_affichage VARCHAR(255) DEFAULT NULL, img_logo VARCHAR(255) DEFAULT NULL, info_id VARCHAR(255) DEFAULT NULL, info_adresse VARCHAR(255) DEFAULT NULL, info_cp VARCHAR(255) DEFAULT NULL, info_ville VARCHAR(255) DEFAULT NULL, info_pays VARCHAR(255) DEFAULT NULL, info_tel VARCHAR(255) DEFAULT NULL, info_siret VARCHAR(255) DEFAULT NULL, info_email VARCHAR(255) DEFAULT NULL, info_chambre VARCHAR(255) DEFAULT NULL, date_version VARCHAR(255) DEFAULT NULL, date_valeurs VARCHAR(255) DEFAULT NULL, facture_objet VARCHAR(255) DEFAULT NULL, facture_title VARCHAR(255) DEFAULT NULL, facture_footer VARCHAR(255) DEFAULT NULL, phase VARCHAR(255) DEFAULT NULL, payement_version VARCHAR(255) DEFAULT NULL, payement_id VARCHAR(255) DEFAULT NULL, payement_key VARCHAR(255) DEFAULT NULL, payement_vad VARCHAR(255) DEFAULT NULL, payement_pk VARCHAR(255) DEFAULT NULL, payement_sk VARCHAR(255) DEFAULT NULL, prix_casque VARCHAR(255) DEFAULT NULL, INDEX IDX_B669D3CFF631228 (etablissement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE configmobile ADD CONSTRAINT FK_B669D3CFF631228 FOREIGN KEY (etablissement_id) REFERENCES etablissement (id)');
     /*   $this->addSql('ALTER TABLE etablissement CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE service_en_chambre CHANGE contenu contenu VARCHAR(20000) NOT NULL');*/
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE configmobile DROP FOREIGN KEY FK_B669D3CFF631228');
        $this->addSql('DROP TABLE configmobile');
        /*$this->addSql('ALTER TABLE etablissement CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE service_en_chambre CHANGE contenu contenu MEDIUMTEXT NOT NULL');*/
    }
}
