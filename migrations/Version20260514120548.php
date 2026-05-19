<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260514120548 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('UPDATE favoris f INNER JOIN application a ON a.id = f.id_element SET f.idEtablissement = a.etablissement_id WHERE f.idEtablissement = 0 OR f.idEtablissement IS NULL');
        $this->addSql('UPDATE favoris f INNER JOIN categories c ON LOWER(c.nom) = LOWER(f.nom_categorie) SET f.idCategorie = c.id WHERE f.idCategorie IS NULL AND f.nom_categorie IS NOT NULL');
        $this->addSql('ALTER TABLE favoris ADD CONSTRAINT FK_8933C432EA190502 FOREIGN KEY (idEtablissement) REFERENCES etablissement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favoris ADD CONSTRAINT FK_8933C432B597FD62 FOREIGN KEY (idCategorie) REFERENCES categories (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_8933C432EA190502 ON favoris (idEtablissement)');
        $this->addSql('CREATE INDEX IDX_8933C432B597FD62 ON favoris (idCategorie)');
    //     $this->addSql('ALTER TABLE lancer_annonce CHANGE date_envoie date_envoie DATETIME NOT NULL');
    //     $this->addSql('ALTER TABLE service_en_chambre CHANGE contenu contenu VARCHAR(20000) NOT NULL');
    //     $this->addSql('ALTER TABLE user CHANGE dernier_temp dernier_temp DATETIME NOT NULL, CHANGE tentative_export tentative_export INT NOT NULL');
    //     $this->addSql('DROP INDEX IDX_75EA56E0FB7336F0 ON messenger_messages');
    //     $this->addSql('DROP INDEX IDX_75EA56E0E3BD61CE ON messenger_messages');
    //     $this->addSql('DROP INDEX IDX_75EA56E016BA31DB ON messenger_messages');
    //     $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages (queue_name, available_at, delivered_at, id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        // $this->addSql('ALTER TABLE etablissement CHANGE id id INT AUTO_INCREMENT NOT NULL');
        // $this->addSql('ALTER TABLE favoris DROP FOREIGN KEY FK_8933C432EA190502');
        // $this->addSql('ALTER TABLE favoris DROP FOREIGN KEY FK_8933C432B597FD62');
        // $this->addSql('DROP INDEX IDX_8933C432EA190502 ON favoris');
        // $this->addSql('DROP INDEX IDX_8933C432B597FD62 ON favoris');
        // $this->addSql('ALTER TABLE favoris ADD categorie_id INT DEFAULT NULL, DROP idEtablissement, CHANGE idCategorie etablissement_id INT DEFAULT NULL');
        // $this->addSql('ALTER TABLE favoris ADD CONSTRAINT FK_8933C432BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categories (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        // $this->addSql('ALTER TABLE favoris ADD CONSTRAINT FK_8933C432FF631228 FOREIGN KEY (etablissement_id) REFERENCES etablissement (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        // $this->addSql('CREATE INDEX IDX_8933C432FF631228 ON favoris (etablissement_id)');
        // $this->addSql('CREATE INDEX IDX_8933C432BCF5E72D ON favoris (categorie_id)');
        // $this->addSql('ALTER TABLE lancer_annonce CHANGE date_envoie date_envoie DATETIME DEFAULT NULL');
        // $this->addSql('DROP INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages');
        // $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        // $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        // $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        // $this->addSql('ALTER TABLE service_en_chambre CHANGE contenu contenu MEDIUMTEXT NOT NULL');
        // $this->addSql('ALTER TABLE `user` CHANGE dernier_temp dernier_temp DATETIME DEFAULT NULL, CHANGE tentative_export tentative_export INT DEFAULT NULL');
    }
}
