<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240208145629 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        if (!$schema->getTable('categories')->hasColumn('background')) {
            $this->addSql('ALTER TABLE categories ADD background VARCHAR(255) DEFAULT NULL');
        }
        
        $this->addSql('ALTER TABLE categories 
            CHANGE nom nom VARCHAR(255) DEFAULT NULL, 
            CHANGE titre titre VARCHAR(255) DEFAULT NULL, 
            CHANGE active active INT DEFAULT NULL, 
            CHANGE position position INT DEFAULT NULL'
        );
    
        // Supprimer cette ligne pour éviter la modification de la colonne 'id' de la table 'etablissement'
        // $this->addSql('ALTER TABLE etablissement CHANGE id id INT NOT NULL');
    }
    
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
       // $this->addSql('ALTER TABLE etablissement CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE categories DROP background, CHANGE nom nom VARCHAR(255) NOT NULL, CHANGE titre titre VARCHAR(255) NOT NULL, CHANGE active active INT NOT NULL, CHANGE position position INT NOT NULL');
    }
}
