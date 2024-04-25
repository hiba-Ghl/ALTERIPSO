<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240425095952 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categorie_livreaudio ADD etablissement_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE categorie_livreaudio ADD CONSTRAINT FK_D1F677AFFF631228 FOREIGN KEY (etablissement_id) REFERENCES etablissement (id)');
        $this->addSql('CREATE INDEX IDX_D1F677AFFF631228 ON categorie_livreaudio (etablissement_id)');
       // $this->addSql('ALTER TABLE etablissement CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE service_en_chambre CHANGE contenu contenu VARCHAR(20000) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categorie_livreaudio DROP FOREIGN KEY FK_D1F677AFFF631228');
        $this->addSql('DROP INDEX IDX_D1F677AFFF631228 ON categorie_livreaudio');
        $this->addSql('ALTER TABLE categorie_livreaudio DROP etablissement_id');
      //  $this->addSql('ALTER TABLE etablissement CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE service_en_chambre CHANGE contenu contenu MEDIUMTEXT NOT NULL');
    }
}
