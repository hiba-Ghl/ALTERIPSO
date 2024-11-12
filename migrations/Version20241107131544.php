<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241107131544 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
     //   $this->addSql('ALTER TABLE etablissement DROP services_id, CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE service_en_chambre CHANGE contenu contenu VARCHAR(20000) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
      //  $this->addSql('ALTER TABLE etablissement ADD services_id INT DEFAULT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE service_en_chambre CHANGE contenu contenu MEDIUMTEXT NOT NULL');
    }
}
