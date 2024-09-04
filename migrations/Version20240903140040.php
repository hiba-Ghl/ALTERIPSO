<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240903140040 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        //$this->addSql('ALTER TABLE etablissement CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE historiquegratuite ADD CONSTRAINT FK_70311A1FFF631228 FOREIGN KEY (etablissement_id) REFERENCES etablissement (id)');
        $this->addSql('ALTER TABLE jeux ADD CONSTRAINT FK_3755B50DFF631228 FOREIGN KEY (etablissement_id) REFERENCES etablissement (id)');
        $this->addSql('ALTER TABLE livreaudio ADD CONSTRAINT FK_4F3FBD03FF631228 FOREIGN KEY (etablissement_id) REFERENCES etablissement (id)');
        $this->addSql('ALTER TABLE livreaudio ADD CONSTRAINT FK_4F3FBD03BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie_livreaudio (id)');
        $this->addSql('ALTER TABLE radio ADD CONSTRAINT FK_E0461B0FFF631228 FOREIGN KEY (etablissement_id) REFERENCES etablissement (id)');
        $this->addSql('ALTER TABLE radio ADD CONSTRAINT FK_E0461B0FBCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie_radio (id)');
        $this->addSql('ALTER TABLE resultat_questionnaire ADD CONSTRAINT FK_2BB0D92EFF631228 FOREIGN KEY (etablissement_id) REFERENCES etablissement (id)');
        $this->addSql('ALTER TABLE resultat_questionnaire ADD CONSTRAINT FK_2BB0D92ECE07E8FF FOREIGN KEY (questionnaire_id) REFERENCES questionnaire (id)');
        $this->addSql('ALTER TABLE resultat_questionnaire ADD CONSTRAINT FK_2BB0D92E9B177F54 FOREIGN KEY (chambre_id) REFERENCES chambre (id)');
        $this->addSql('ALTER TABLE resultat_questionnaire ADD CONSTRAINT FK_2BB0D92EED5CA9E6 FOREIGN KEY (service_id) REFERENCES service_etablissement (id)');
        //$this->addSql('ALTER TABLE service_en_chambre CHANGE contenu contenu VARCHAR(20000) NOT NULL');
        $this->addSql('ALTER TABLE user DROP liste_questionnaire, DROP resultat_questionnaire, DROP ajouter_questionnaire, DROP sauvegarder_questionnaire, DROP modifier_questionnaire, DROP supprimer_questionnaire, DROP imprimer_result_question, DROP exporter_pdf_resultat_question, DROP exporter_excel_resultat_question, DROP questionnaire, DROP liste_resultat_questionnaire');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        //$this->addSql('ALTER TABLE etablissement CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE historiquegratuite DROP FOREIGN KEY FK_70311A1FFF631228');
        $this->addSql('ALTER TABLE jeux DROP FOREIGN KEY FK_3755B50DFF631228');
        $this->addSql('ALTER TABLE livreaudio DROP FOREIGN KEY FK_4F3FBD03FF631228');
        $this->addSql('ALTER TABLE livreaudio DROP FOREIGN KEY FK_4F3FBD03BCF5E72D');
        $this->addSql('ALTER TABLE radio DROP FOREIGN KEY FK_E0461B0FFF631228');
        $this->addSql('ALTER TABLE radio DROP FOREIGN KEY FK_E0461B0FBCF5E72D');
        $this->addSql('ALTER TABLE resultat_questionnaire DROP FOREIGN KEY FK_2BB0D92EFF631228');
        $this->addSql('ALTER TABLE resultat_questionnaire DROP FOREIGN KEY FK_2BB0D92ECE07E8FF');
        $this->addSql('ALTER TABLE resultat_questionnaire DROP FOREIGN KEY FK_2BB0D92E9B177F54');
        $this->addSql('ALTER TABLE resultat_questionnaire DROP FOREIGN KEY FK_2BB0D92EED5CA9E6');
        //$this->addSql('ALTER TABLE service_en_chambre CHANGE contenu contenu MEDIUMTEXT NOT NULL');
        $this->addSql('ALTER TABLE `user` ADD liste_questionnaire INT NOT NULL, ADD resultat_questionnaire INT NOT NULL, ADD ajouter_questionnaire INT NOT NULL, ADD sauvegarder_questionnaire INT NOT NULL, ADD modifier_questionnaire INT NOT NULL, ADD supprimer_questionnaire INT NOT NULL, ADD imprimer_result_question INT NOT NULL, ADD exporter_pdf_resultat_question INT NOT NULL, ADD exporter_excel_resultat_question INT NOT NULL, ADD questionnaire INT NOT NULL, ADD liste_resultat_questionnaire INT NOT NULL');
    }
}
