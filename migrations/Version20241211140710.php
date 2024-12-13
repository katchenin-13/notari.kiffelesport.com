<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241211140710 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE ligneversementhonoraire');
        $this->addSql('ALTER TABLE marche CHANGE solde solde VARCHAR(255) NOT NULL, CHANGE active active TINYINT(1) NOT NULL, CHANGE etat etat JSON NOT NULL, CHANGE datecreation datecreation DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ligneversementhonoraire (id INT AUTO_INCREMENT NOT NULL, dateversementhonoraire DATETIME NOT NULL, montantverse VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE marche CHANGE solde solde VARCHAR(255) DEFAULT NULL, CHANGE active active TINYINT(1) DEFAULT NULL, CHANGE etat etat JSON DEFAULT NULL, CHANGE datecreation datecreation DATETIME NOT NULL');
    }
}
