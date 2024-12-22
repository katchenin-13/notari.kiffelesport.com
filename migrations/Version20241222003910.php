<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241222003910 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rapporthebdomadaire (id INT AUTO_INCREMENT NOT NULL, fichier_id INT DEFAULT NULL, utilisateur_id INT NOT NULL, employe_id INT DEFAULT NULL, daterapport DATETIME DEFAULT NULL, libelle VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_717ED2B4F915CFE (fichier_id), INDEX IDX_717ED2B4FB88E14F (utilisateur_id), INDEX IDX_717ED2B41B65292 (employe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rapporthebdomadaire ADD CONSTRAINT FK_717ED2B4F915CFE FOREIGN KEY (fichier_id) REFERENCES _admin_param_fichier (id)');
        $this->addSql('ALTER TABLE rapporthebdomadaire ADD CONSTRAINT FK_717ED2B4FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES _admin_user_utilisateur (id)');
        $this->addSql('ALTER TABLE rapporthebdomadaire ADD CONSTRAINT FK_717ED2B41B65292 FOREIGN KEY (employe_id) REFERENCES _admin_employe (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rapporthebdomadaire DROP FOREIGN KEY FK_717ED2B4F915CFE');
        $this->addSql('ALTER TABLE rapporthebdomadaire DROP FOREIGN KEY FK_717ED2B4FB88E14F');
        $this->addSql('ALTER TABLE rapporthebdomadaire DROP FOREIGN KEY FK_717ED2B41B65292');
        $this->addSql('DROP TABLE rapporthebdomadaire');
    }
}
