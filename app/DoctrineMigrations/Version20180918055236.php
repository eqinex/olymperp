<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180918055236 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE document ADD parent_document INT DEFAULT NULL, DROP supplementary_agreement, DROP supplementary_agreement_date');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A7647A426A7 FOREIGN KEY (parent_document) REFERENCES document (id)');
        $this->addSql('CREATE INDEX IDX_D8698A7647A426A7 ON document (parent_document)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A7647A426A7');
        $this->addSql('DROP INDEX IDX_D8698A7647A426A7 ON document');
        $this->addSql('ALTER TABLE document ADD supplementary_agreement VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD supplementary_agreement_date DATETIME DEFAULT NULL, DROP parent_document');
    }
}
