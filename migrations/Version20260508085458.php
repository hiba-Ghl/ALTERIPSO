<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260508085458 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE etablissement ADD rss VARCHAR(255) DEFAULT NULL');
    //     $this->addSql('ALTER TABLE lancer_annonce CHANGE date_envoie date_envoie DATETIME NOT NULL');
    //     $this->addSql('ALTER TABLE service_en_chambre CHANGE contenu contenu VARCHAR(20000) NOT NULL');
    //     $this->addSql('ALTER TABLE user CHANGE dernier_temp dernier_temp DATETIME NOT NULL, CHANGE tentative_export tentative_export INT NOT NULL');
    //     $this->addSql('DROP INDEX IDX_75EA56E0FB7336F0 ON messenger_messages');
    //     $this->addSql('DROP INDEX IDX_75EA56E0E3BD61CE ON messenger_messages');
    //     $this->addSql('DROP INDEX IDX_75EA56E016BA31DB ON messenger_messages');
    //     $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages (queue_name, available_at, delivered_at, id)');
    // 
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        // $this->addSql('ALTER TABLE etablissement DROP rss, CHANGE id id INT AUTO_INCREMENT NOT NULL');
        // $this->addSql('ALTER TABLE lancer_annonce CHANGE date_envoie date_envoie DATETIME DEFAULT NULL');
        // $this->addSql('DROP INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages');
        // $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        // $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        // $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        // $this->addSql('ALTER TABLE service_en_chambre CHANGE contenu contenu MEDIUMTEXT NOT NULL');
        // $this->addSql('ALTER TABLE `user` CHANGE dernier_temp dernier_temp DATETIME DEFAULT NULL, CHANGE tentative_export tentative_export INT DEFAULT NULL');
    }
}
