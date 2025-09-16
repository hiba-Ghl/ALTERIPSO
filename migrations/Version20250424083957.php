<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250424083957 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE lancer_annonce (id INT AUTO_INCREMENT NOT NULL, id_chambre INT NOT NULL, id_annonce INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lancer_radio (id INT AUTO_INCREMENT NOT NULL, id_chambre INT NOT NULL, id_radio INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lancer_tv (id INT AUTO_INCREMENT NOT NULL, id_chambre INT NOT NULL, id_tv INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        // $this->addSql('DROP TABLE bouquetbyroom');
        // $this->addSql('DROP TABLE bouquetprolongation');
        $this->addSql('ALTER TABLE annonce ADD es_message VARCHAR(255) DEFAULT NULL, ADD pt_message VARCHAR(255) DEFAULT NULL, ADD it_message VARCHAR(255) DEFAULT NULL, ADD ru_message VARCHAR(255) DEFAULT NULL, ADD de_message VARCHAR(255) DEFAULT NULL, ADD zh_message VARCHAR(255) DEFAULT NULL, ADD ar_message VARCHAR(255) DEFAULT NULL, ADD active TINYINT(1) NOT NULL, ADD fr_message VARCHAR(255) DEFAULT NULL, ADD en_message VARCHAR(255) DEFAULT NULL, DROP fr, DROP en, DROP es, DROP pt, DROP it, DROP ru, DROP de, DROP zh, DROP ar');
        $this->addSql('ALTER TABLE chambre DROP etat, DROP login, DROP mdp');
        // $this->addSql('ALTER TABLE etablissement CHANGE id id INT NOT NULL');
        // $this->addSql('ALTER TABLE service_en_chambre CHANGE contenu contenu VARCHAR(20000) NOT NULL');
        // $this->addSql('ALTER TABLE user CHANGE dernier_temp dernier_temp DATETIME NOT NULL, CHANGE tentative_export tentative_export INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        // $this->addSql('CREATE TABLE bouquetbyroom (id INT AUTO_INCREMENT NOT NULL, idbouquet VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, idroom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = MyISAM COMMENT = \'\' ');
        // $this->addSql('CREATE TABLE bouquetprolongation (id INT AUTO_INCREMENT NOT NULL, idroom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, idbouquet INT NOT NULL, dateinprol DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('DROP TABLE lancer_annonce');
        $this->addSql('DROP TABLE lancer_radio');
        $this->addSql('DROP TABLE lancer_tv');
        $this->addSql('ALTER TABLE annonce ADD fr LONGTEXT DEFAULT NULL, ADD en LONGTEXT DEFAULT NULL, ADD es LONGTEXT DEFAULT NULL, ADD pt LONGTEXT DEFAULT NULL, ADD it LONGTEXT DEFAULT NULL, ADD ru LONGTEXT DEFAULT NULL, ADD de LONGTEXT DEFAULT NULL, ADD zh LONGTEXT DEFAULT NULL, ADD ar LONGTEXT DEFAULT NULL, DROP es_message, DROP pt_message, DROP it_message, DROP ru_message, DROP de_message, DROP zh_message, DROP ar_message, DROP active, DROP fr_message, DROP en_message');
        $this->addSql('ALTER TABLE chambre ADD etat VARCHAR(255) DEFAULT NULL, ADD login VARCHAR(255) DEFAULT NULL, ADD mdp VARCHAR(255) DEFAULT NULL');
        // $this->addSql('ALTER TABLE etablissement CHANGE id id INT AUTO_INCREMENT NOT NULL');
        // $this->addSql('ALTER TABLE service_en_chambre CHANGE contenu contenu MEDIUMTEXT NOT NULL');
        // $this->addSql('ALTER TABLE `user` CHANGE dernier_temp dernier_temp DATETIME DEFAULT NULL, CHANGE tentative_export tentative_export INT DEFAULT NULL');
    }
}
