<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180813060841 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE document_file (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, document_id INT DEFAULT NULL, file_size VARCHAR(255) NOT NULL, format VARCHAR(255) NOT NULL, uploaded_at DATETIME NOT NULL, deleted TINYINT(1) DEFAULT NULL, file_name LONGTEXT NOT NULL, stored_file_name LONGTEXT NOT NULL, stored_preview_file_name LONGTEXT DEFAULT NULL, stored_file_dir LONGTEXT DEFAULT NULL, INDEX IDX_2B2BBA837E3C61F9 (owner_id), INDEX IDX_2B2BBA83C33F7837 (document_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE document_file ADD CONSTRAINT FK_2B2BBA837E3C61F9 FOREIGN KEY (owner_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE document_file ADD CONSTRAINT FK_2B2BBA83C33F7837 FOREIGN KEY (document_id) REFERENCES document (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE document_file');
    }
}
