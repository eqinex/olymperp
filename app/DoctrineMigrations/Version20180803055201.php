<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180803055201 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE document_signatory (id INT AUTO_INCREMENT NOT NULL, document_id INT DEFAULT NULL, signatory_id INT DEFAULT NULL, approved TINYINT(1) NOT NULL, INDEX IDX_B9005309C33F7837 (document_id), INDEX IDX_B900530955E3D1E0 (signatory_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE document_signatory ADD CONSTRAINT FK_B9005309C33F7837 FOREIGN KEY (document_id) REFERENCES document (id)');
        $this->addSql('ALTER TABLE document_signatory ADD CONSTRAINT FK_B900530955E3D1E0 FOREIGN KEY (signatory_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE document ADD signatories_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A76EF4E3C2B FOREIGN KEY (signatories_id) REFERENCES document_signatory (id)');
        $this->addSql('CREATE INDEX idx_signatories_id ON document (signatories_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A76EF4E3C2B');
        $this->addSql('DROP TABLE document_signatory');
        $this->addSql('DROP INDEX idx_signatories_id ON document');
        $this->addSql('ALTER TABLE document DROP signatories_id');
    }
}
