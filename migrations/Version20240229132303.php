<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240229132303 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chambre ADD chaine_id INT DEFAULT NULL, DROP chaine');
        $this->addSql('ALTER TABLE chambre ADD CONSTRAINT FK_C509E4FF3129D93D FOREIGN KEY (chaine_id) REFERENCES television (id)');
        $this->addSql('CREATE INDEX IDX_C509E4FF3129D93D ON chambre (chaine_id)');
      //  $this->addSql('ALTER TABLE etablissement CHANGE id id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chambre DROP FOREIGN KEY FK_C509E4FF3129D93D');
        $this->addSql('DROP INDEX IDX_C509E4FF3129D93D ON chambre');
        $this->addSql('ALTER TABLE chambre ADD chaine VARCHAR(255) DEFAULT NULL, DROP chaine_id');
      //  $this->addSql('ALTER TABLE etablissement CHANGE id id INT AUTO_INCREMENT NOT NULL');
    }
}
