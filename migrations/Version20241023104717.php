<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241023104717 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
    /*   $this->addSql('ALTER TABLE etablissement DROP FOREIGN KEY FK_20FD592CAEF5A6C1');
        $this->addSql('DROP INDEX IDX_20FD592CAEF5A6C1 ON etablissement');
        $this->addSql('ALTER TABLE etablissement DROP services_id, CHANGE id id INT NOT NULL');*/
      //  $this->addSql('ALTER TABLE service_en_chambre CHANGE contenu contenu VARCHAR(20000) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
      /*  $this->addSql('ALTER TABLE etablissement ADD services_id INT DEFAULT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE etablissement ADD CONSTRAINT FK_20FD592CAEF5A6C1 FOREIGN KEY (services_id) REFERENCES services (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_20FD592CAEF5A6C1 ON etablissement (services_id)');*/
     //   $this->addSql('ALTER TABLE service_en_chambre CHANGE contenu contenu MEDIUMTEXT NOT NULL');
    }
}
