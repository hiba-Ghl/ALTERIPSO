<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250124154334 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE config_app (id INT AUTO_INCREMENT NOT NULL, etablissement_id INT DEFAULT NULL, server_host_rsmartv VARCHAR(255) NOT NULL, server_host_paytv VARCHAR(255) NOT NULL, server_host_vod VARCHAR(255) NOT NULL, server_host_livreaudio VARCHAR(255) NOT NULL, server_host_toukan VARCHAR(255) NOT NULL, server_host_canalplus VARCHAR(255) NOT NULL, server_host_mail VARCHAR(255) NOT NULL, database_name_rsmartv VARCHAR(255) NOT NULL, database_user_rsmartv VARCHAR(255) NOT NULL, database_pass_rsmartv VARCHAR(255) NOT NULL, database_name_paytv VARCHAR(255) NOT NULL, database_user_paytv VARCHAR(255) NOT NULL, database_pass_paytv VARCHAR(255) NOT NULL, database_name_vod VARCHAR(255) NOT NULL, database_user_vod VARCHAR(255) NOT NULL, database_pass_vod VARCHAR(255) NOT NULL, database_name_canalplus VARCHAR(255) NOT NULL, database_user_canalplus VARCHAR(255) NOT NULL, database_pass_canalplus VARCHAR(255) NOT NULL, enable_television VARCHAR(255) NOT NULL, enable_statistiquechainetv VARCHAR(255) NOT NULL, enable_radio VARCHAR(255) NOT NULL, enable_service VARCHAR(255) NOT NULL, enable_vod VARCHAR(255) NOT NULL, enable_musique VARCHAR(255) NOT NULL, enable_livreaudio VARCHAR(255) NOT NULL, enable_jeux VARCHAR(255) NOT NULL, enable_servicespayants VARCHAR(255) NOT NULL, enable_questionnaire VARCHAR(255) NOT NULL, enable_application VARCHAR(255) NOT NULL, enable_enregistrement VARCHAR(255) NOT NULL, enable_annonces VARCHAR(255) NOT NULL, enable_chartes VARCHAR(255) NOT NULL, enable_videos VARCHAR(255) NOT NULL, enable_support_connect VARCHAR(255) NOT NULL, enable_message_personnels VARCHAR(255) NOT NULL, enable_rapplication VARCHAR(255) NOT NULL, enable_categories VARCHAR(255) NOT NULL, checkin VARCHAR(255) NOT NULL, checkout VARCHAR(255) NOT NULL, codeportail VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_D62F11C0FF631228 (etablissement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE config_app ADD CONSTRAINT FK_D62F11C0FF631228 FOREIGN KEY (etablissement_id) REFERENCES etablissement (id)');
        // $this->addSql('ALTER TABLE etablissement CHANGE id id INT NOT NULL');
        // $this->addSql('ALTER TABLE service_en_chambre CHANGE contenu contenu VARCHAR(20000) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE config_app DROP FOREIGN KEY FK_D62F11C0FF631228');
        $this->addSql('DROP TABLE config_app');
        // $this->addSql('ALTER TABLE etablissement CHANGE id id INT AUTO_INCREMENT NOT NULL');
        // $this->addSql('ALTER TABLE service_en_chambre CHANGE contenu contenu MEDIUMTEXT NOT NULL');
    }
}
