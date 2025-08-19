<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250818234104 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire_remise_acte DROP FOREIGN KEY FK_F5F539E4611C0C56');
        $this->addSql('DROP TABLE commentaire_remise_acte');
        $this->addSql('ALTER TABLE commentaire_eng DROP FOREIGN KEY FK_294050D611C0C56');
        $this->addSql('ALTER TABLE commentaire_eng ADD CONSTRAINT FK_294050D611C0C56 FOREIGN KEY (dossier_id) REFERENCES dossier (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE commentaire_identification DROP FOREIGN KEY FK_AB9B9A07611C0C56');
        $this->addSql('ALTER TABLE commentaire_identification ADD CONSTRAINT FK_AB9B9A07611C0C56 FOREIGN KEY (dossier_id) REFERENCES dossier (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE commentaire_obtention DROP FOREIGN KEY FK_F5A22D84611C0C56');
        $this->addSql('ALTER TABLE commentaire_obtention ADD CONSTRAINT FK_F5A22D84611C0C56 FOREIGN KEY (dossier_id) REFERENCES dossier (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE commentaire_paiement DROP FOREIGN KEY FK_43CDB2C2611C0C56');
        $this->addSql('ALTER TABLE commentaire_paiement ADD CONSTRAINT FK_43CDB2C2611C0C56 FOREIGN KEY (dossier_id) REFERENCES dossier (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE commentaire_piece DROP FOREIGN KEY FK_C146C33F611C0C56');
        $this->addSql('ALTER TABLE commentaire_piece ADD CONSTRAINT FK_C146C33F611C0C56 FOREIGN KEY (dossier_id) REFERENCES dossier (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE commentaire_redaction DROP FOREIGN KEY FK_A6379BD2611C0C56');
        $this->addSql('ALTER TABLE commentaire_redaction ADD CONSTRAINT FK_A6379BD2611C0C56 FOREIGN KEY (dossier_id) REFERENCES dossier (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE commentaire_signature DROP FOREIGN KEY FK_211F8E76611C0C56');
        $this->addSql('ALTER TABLE commentaire_signature ADD CONSTRAINT FK_211F8E76611C0C56 FOREIGN KEY (dossier_id) REFERENCES dossier (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commentaire_remise_acte (id INT AUTO_INCREMENT NOT NULL, dossier_id INT DEFAULT NULL, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, active TINYINT(1) NOT NULL, INDEX IDX_F5F539E4611C0C56 (dossier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE commentaire_remise_acte ADD CONSTRAINT FK_F5F539E4611C0C56 FOREIGN KEY (dossier_id) REFERENCES dossier (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE commentaire_eng DROP FOREIGN KEY FK_294050D611C0C56');
        $this->addSql('ALTER TABLE commentaire_eng ADD CONSTRAINT FK_294050D611C0C56 FOREIGN KEY (dossier_id) REFERENCES dossier (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE commentaire_identification DROP FOREIGN KEY FK_AB9B9A07611C0C56');
        $this->addSql('ALTER TABLE commentaire_identification ADD CONSTRAINT FK_AB9B9A07611C0C56 FOREIGN KEY (dossier_id) REFERENCES dossier (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE commentaire_obtention DROP FOREIGN KEY FK_F5A22D84611C0C56');
        $this->addSql('ALTER TABLE commentaire_obtention ADD CONSTRAINT FK_F5A22D84611C0C56 FOREIGN KEY (dossier_id) REFERENCES dossier (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE commentaire_paiement DROP FOREIGN KEY FK_43CDB2C2611C0C56');
        $this->addSql('ALTER TABLE commentaire_paiement ADD CONSTRAINT FK_43CDB2C2611C0C56 FOREIGN KEY (dossier_id) REFERENCES dossier (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE commentaire_piece DROP FOREIGN KEY FK_C146C33F611C0C56');
        $this->addSql('ALTER TABLE commentaire_piece ADD CONSTRAINT FK_C146C33F611C0C56 FOREIGN KEY (dossier_id) REFERENCES dossier (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE commentaire_redaction DROP FOREIGN KEY FK_A6379BD2611C0C56');
        $this->addSql('ALTER TABLE commentaire_redaction ADD CONSTRAINT FK_A6379BD2611C0C56 FOREIGN KEY (dossier_id) REFERENCES dossier (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE commentaire_signature DROP FOREIGN KEY FK_211F8E76611C0C56');
        $this->addSql('ALTER TABLE commentaire_signature ADD CONSTRAINT FK_211F8E76611C0C56 FOREIGN KEY (dossier_id) REFERENCES dossier (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
