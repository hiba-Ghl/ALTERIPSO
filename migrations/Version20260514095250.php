<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260514095250 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE favoris (id INT AUTO_INCREMENT NOT NULL, etablissement_id INT DEFAULT NULL, categorie_id INT DEFAULT NULL, nom_categorie VARCHAR(255) DEFAULT NULL, id_element INT DEFAULT NULL, nom_element VARCHAR(255) DEFAULT NULL, INDEX IDX_8933C432FF631228 (etablissement_id), INDEX IDX_8933C432BCF5E72D (categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE favoris ADD CONSTRAINT FK_8933C432FF631228 FOREIGN KEY (etablissement_id) REFERENCES etablissement (id)');
        $this->addSql('ALTER TABLE favoris ADD CONSTRAINT FK_8933C432BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categories (id)');
        // $this->addSql('ALTER TABLE etablissement CHANGE id id INT NOT NULL');
        // $this->addSql('ALTER TABLE lancer_annonce CHANGE date_envoie date_envoie DATETIME NOT NULL');
        // $this->addSql('ALTER TABLE service_en_chambre CHANGE contenu contenu VARCHAR(20000) NOT NULL');
        // $this->addSql('ALTER TABLE user CHANGE dernier_temp dernier_temp DATETIME NOT NULL, CHANGE tentative_export tentative_export INT NOT NULL');
        // $this->addSql('DROP INDEX IDX_75EA56E0FB7336F0 ON messenger_messages');
        // $this->addSql('DROP INDEX IDX_75EA56E0E3BD61CE ON messenger_messages');
        // $this->addSql('DROP INDEX IDX_75EA56E016BA31DB ON messenger_messages');
        // $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages (queue_name, available_at, delivered_at, id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        // $this->addSql('ALTER TABLE favoris DROP FOREIGN KEY FK_8933C432FF631228');
        // $this->addSql('ALTER TABLE favoris DROP FOREIGN KEY FK_8933C432BCF5E72D');
        // $this->addSql('DROP TABLE favoris');
        // $this->addSql('ALTER TABLE etablissement CHANGE id id INT AUTO_INCREMENT NOT NULL');
        // $this->addSql('ALTER TABLE lancer_annonce CHANGE date_envoie date_envoie DATETIME DEFAULT NULL');
        // $this->addSql('DROP INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages');
        // $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        // $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        // $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        // $this->addSql('ALTER TABLE service_en_chambre CHANGE contenu contenu MEDIUMTEXT NOT NULL');
        // $this->addSql('ALTER TABLE `user` CHANGE dernier_temp dernier_temp DATETIME DEFAULT NULL, CHANGE tentative_export tentative_export INT DEFAULT NULL');
    }
}
