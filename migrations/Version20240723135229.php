<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240723135229 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE resultat_questionnaire (id INT AUTO_INCREMENT NOT NULL, etablissement_id INT DEFAULT NULL, questionnaire_id INT DEFAULT NULL, chambre_id INT DEFAULT NULL, service_id INT DEFAULT NULL, vote INT NOT NULL, date VARCHAR(255) NOT NULL, INDEX IDX_2BB0D92EFF631228 (etablissement_id), INDEX IDX_2BB0D92ECE07E8FF (questionnaire_id), INDEX IDX_2BB0D92E9B177F54 (chambre_id), INDEX IDX_2BB0D92EED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE resultat_questionnaire ADD CONSTRAINT FK_2BB0D92EFF631228 FOREIGN KEY (etablissement_id) REFERENCES etablissement (id)');
        $this->addSql('ALTER TABLE resultat_questionnaire ADD CONSTRAINT FK_2BB0D92ECE07E8FF FOREIGN KEY (questionnaire_id) REFERENCES questionnaire (id)');
        $this->addSql('ALTER TABLE resultat_questionnaire ADD CONSTRAINT FK_2BB0D92E9B177F54 FOREIGN KEY (chambre_id) REFERENCES chambre (id)');
        $this->addSql('ALTER TABLE resultat_questionnaire ADD CONSTRAINT FK_2BB0D92EED5CA9E6 FOREIGN KEY (service_id) REFERENCES service_etablissement (id)');
       // $this->addSql('ALTER TABLE etablissement CHANGE id id INT NOT NULL');
        //$this->addSql('ALTER TABLE service_en_chambre CHANGE contenu contenu VARCHAR(20000) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE resultat_questionnaire DROP FOREIGN KEY FK_2BB0D92EFF631228');
        $this->addSql('ALTER TABLE resultat_questionnaire DROP FOREIGN KEY FK_2BB0D92ECE07E8FF');
        $this->addSql('ALTER TABLE resultat_questionnaire DROP FOREIGN KEY FK_2BB0D92E9B177F54');
        $this->addSql('ALTER TABLE resultat_questionnaire DROP FOREIGN KEY FK_2BB0D92EED5CA9E6');
        $this->addSql('DROP TABLE resultat_questionnaire');
      //  $this->addSql('ALTER TABLE etablissement CHANGE id id INT AUTO_INCREMENT NOT NULL');
       // $this->addSql('ALTER TABLE service_en_chambre CHANGE contenu contenu MEDIUMTEXT NOT NULL');
    }
}
