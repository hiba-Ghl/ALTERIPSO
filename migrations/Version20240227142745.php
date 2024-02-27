<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240227142745 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE historiquegratuite (id INT AUTO_INCREMENT NOT NULL, etablissement_id INT NOT NULL, date VARCHAR(255) DEFAULT NULL, datein VARCHAR(255) DEFAULT NULL, dateout VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, INDEX IDX_70311A1FFF631228 (etablissement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE historiquegratuite ADD CONSTRAINT FK_70311A1FFF631228 FOREIGN KEY (etablissement_id) REFERENCES etablissement (id)');
       // $this->addSql('ALTER TABLE etablissement CHANGE id id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE historiquegratuite DROP FOREIGN KEY FK_70311A1FFF631228');
        $this->addSql('DROP TABLE historiquegratuite');
        //$this->addSql('ALTER TABLE etablissement CHANGE id id INT AUTO_INCREMENT NOT NULL');
    }
}
