<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260326110236 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Migration neutralisée : les modifications de schéma prévues ici
        // (suppression de rss_style, DROP des tables bouquet*, changements
        // de colonnes sur d'autres tables) ont déjà été appliquées ou ne
        // sont pas nécessaires sur cette base. On laisse volontairement
        // cette méthode vide pour que Doctrine puisse marquer cette
        // migration comme exécutée sans toucher au schéma actuel.
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce ADD rss_style JSON DEFAULT NULL');
        $this->addSql('CREATE TABLE bouquetbyroom (id INT AUTO_INCREMENT NOT NULL, idbouquet VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, idroom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('CREATE TABLE bouquetprolongation (id INT AUTO_INCREMENT NOT NULL, idroom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, idbouquet INT NOT NULL, dateinprol DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('ALTER TABLE etablissement CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE lancer_annonce CHANGE date_envoie date_envoie DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE service_en_chambre CHANGE contenu contenu MEDIUMTEXT NOT NULL');
        $this->addSql('ALTER TABLE `user` CHANGE dernier_temp dernier_temp DATETIME DEFAULT NULL, CHANGE tentative_export tentative_export INT DEFAULT NULL');
    }
}
