<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240425100508 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE livreaudio (id INT AUTO_INCREMENT NOT NULL, etablissement_id INT DEFAULT NULL, categorie_id INT DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, ip VARCHAR(255) DEFAULT NULL, port VARCHAR(255) DEFAULT NULL, pays VARCHAR(255) DEFAULT NULL, active VARCHAR(255) DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, protocole VARCHAR(255) DEFAULT NULL, INDEX IDX_4F3FBD03FF631228 (etablissement_id), INDEX IDX_4F3FBD03BCF5E72D (categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE livreaudio ADD CONSTRAINT FK_4F3FBD03FF631228 FOREIGN KEY (etablissement_id) REFERENCES etablissement (id)');
        $this->addSql('ALTER TABLE livreaudio ADD CONSTRAINT FK_4F3FBD03BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie_livreaudio (id)');
        //$this->addSql('ALTER TABLE etablissement CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE service_en_chambre CHANGE contenu contenu VARCHAR(20000) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE livreaudio DROP FOREIGN KEY FK_4F3FBD03FF631228');
        $this->addSql('ALTER TABLE livreaudio DROP FOREIGN KEY FK_4F3FBD03BCF5E72D');
        $this->addSql('DROP TABLE livreaudio');
        //$this->addSql('ALTER TABLE etablissement CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE service_en_chambre CHANGE contenu contenu MEDIUMTEXT NOT NULL');
    }
}
