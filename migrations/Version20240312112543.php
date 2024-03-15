<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240312112543 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
      //  $this->addSql('CREATE TABLE support (id INT AUTO_INCREMENT NOT NULL, etablissement_id INT DEFAULT NULL, nom VARCHAR(255) DEFAULT NULL, protocole INT DEFAULT NULL, INDEX IDX_8004EBA5FF631228 (etablissement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        //$this->addSql('ALTER TABLE support ADD CONSTRAINT FK_8004EBA5FF631228 FOREIGN KEY (etablissement_id) REFERENCES etablissement (id)');
        $this->addSql('ALTER TABLE etablissement ADD support_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE etablissement ADD CONSTRAINT FK_20FD592C315B405 FOREIGN KEY (support_id) REFERENCES support (id)');
        $this->addSql('CREATE INDEX IDX_20FD592C315B405 ON etablissement (support_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE etablissement DROP FOREIGN KEY FK_20FD592C315B405');
        $this->addSql('ALTER TABLE support DROP FOREIGN KEY FK_8004EBA5FF631228');
        $this->addSql('DROP TABLE support');
        $this->addSql('DROP INDEX IDX_20FD592C315B405 ON etablissement');
        $this->addSql('ALTER TABLE etablissement DROP support_id');
    }
}
