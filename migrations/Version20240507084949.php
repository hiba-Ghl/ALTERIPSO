<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240507084949 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
       /* $this->addSql('CREATE TABLE questionnaire (id INT AUTO_INCREMENT NOT NULL, etablissement_id INT DEFAULT NULL, service_id INT DEFAULT NULL, question VARCHAR(255) DEFAULT NULL, position VARCHAR(255) DEFAULT NULL, active INT DEFAULT NULL, fr VARCHAR(255) DEFAULT NULL, en VARCHAR(255) DEFAULT NULL, es VARCHAR(255) DEFAULT NULL, pt VARCHAR(255) DEFAULT NULL, it VARCHAR(255) DEFAULT NULL, ru VARCHAR(255) DEFAULT NULL, de VARCHAR(255) DEFAULT NULL, zh VARCHAR(255) DEFAULT NULL, ar VARCHAR(255) DEFAULT NULL, INDEX IDX_7A64DAFFF631228 (etablissement_id), INDEX IDX_7A64DAFED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE questionnaire ADD CONSTRAINT FK_7A64DAFFF631228 FOREIGN KEY (etablissement_id) REFERENCES etablissement (id)');
        $this->addSql('ALTER TABLE questionnaire ADD CONSTRAINT FK_7A64DAFED5CA9E6 FOREIGN KEY (service_id) REFERENCES service_etablissement (id)');
      //  $this->addSql('ALTER TABLE etablissement CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE service_en_chambre CHANGE contenu contenu VARCHAR(20000) NOT NULL');*/
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
       /* $this->addSql('ALTER TABLE questionnaire DROP FOREIGN KEY FK_7A64DAFFF631228');
        $this->addSql('ALTER TABLE questionnaire DROP FOREIGN KEY FK_7A64DAFED5CA9E6');
        $this->addSql('DROP TABLE questionnaire');
      //  $this->addSql('ALTER TABLE etablissement CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE service_en_chambre CHANGE contenu contenu MEDIUMTEXT NOT NULL');*/
    }
}
