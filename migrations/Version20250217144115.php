<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250217144115 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // $this->addSql('ALTER TABLE etablissement CHANGE id id INT NOT NULL');
        // $this->addSql('ALTER TABLE service_en_chambre CHANGE contenu contenu VARCHAR(20000) NOT NULL');
        $this->addSql('ALTER TABLE user ADD sauvgarder_service_payant TINYINT(1) NOT NULL, ADD sauvegarder_chart_patient TINYINT(1) NOT NULL, ADD cocher_meteo TINYINT(1) NOT NULL, ADD cocher_logo TINYINT(1) NOT NULL, ADD modifier_rmobile TINYINT(1) NOT NULL, ADD ajoute_service_etablissement TINYINT(1) NOT NULL, ADD check_support TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        // $this->addSql('ALTER TABLE etablissement CHANGE id id INT AUTO_INCREMENT NOT NULL');
        // $this->addSql('ALTER TABLE service_en_chambre CHANGE contenu contenu MEDIUMTEXT NOT NULL');
        $this->addSql('ALTER TABLE `user` DROP sauvgarder_service_payant, DROP sauvegarder_chart_patient, DROP cocher_meteo, DROP cocher_logo, DROP modifier_rmobile, DROP ajoute_service_etablissement, DROP check_support');
    }
}
