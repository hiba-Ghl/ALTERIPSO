<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240319123033 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE jeux (id INT AUTO_INCREMENT NOT NULL, etablissement_id INT DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, package VARCHAR(255) DEFAULT NULL, active VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, position INT DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, protocole VARCHAR(255) DEFAULT NULL, INDEX IDX_3755B50DFF631228 (etablissement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE jeux ADD CONSTRAINT FK_3755B50DFF631228 FOREIGN KEY (etablissement_id) REFERENCES etablissement (id)');
     //   $this->addSql('ALTER TABLE etablissement CHANGE id id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE jeux DROP FOREIGN KEY FK_3755B50DFF631228');
        $this->addSql('DROP TABLE jeux');
      //  $this->addSql('ALTER TABLE etablissement CHANGE id id INT AUTO_INCREMENT NOT NULL');
    }
}
