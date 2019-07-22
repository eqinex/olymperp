<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181218100318 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE programming_document_file (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, programming_document_id INT DEFAULT NULL, file_size VARCHAR(255) NOT NULL, format VARCHAR(255) NOT NULL, uploaded_at DATETIME NOT NULL, file_name LONGTEXT NOT NULL, stored_file_dir LONGTEXT DEFAULT NULL, INDEX IDX_DB64D66A7E3C61F9 (owner_id), UNIQUE INDEX UNIQ_DB64D66AE3998EDD (programming_document_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE programming_document (id INT AUTO_INCREMENT NOT NULL, programming_document_type_id INT DEFAULT NULL, owner_id INT DEFAULT NULL, project_id INT DEFAULT NULL, file_id INT DEFAULT NULL, created_at DATETIME NOT NULL, designation VARCHAR(255) NOT NULL, number_of_pages INT DEFAULT NULL, format VARCHAR(255) DEFAULT NULL, code VARCHAR(255) NOT NULL, register_number VARCHAR(255) DEFAULT NULL, edition_number VARCHAR(255) DEFAULT NULL, notice LONGTEXT DEFAULT NULL, INDEX IDX_CB97423AE83BB93A (programming_document_type_id), INDEX IDX_CB97423A7E3C61F9 (owner_id), INDEX IDX_CB97423A166D1F9C (project_id), UNIQUE INDEX UNIQ_CB97423A93CB796C (file_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE programming_document_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(60) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE programming_document_file ADD CONSTRAINT FK_DB64D66A7E3C61F9 FOREIGN KEY (owner_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE programming_document_file ADD CONSTRAINT FK_DB64D66AE3998EDD FOREIGN KEY (programming_document_id) REFERENCES programming_document (id)');
        $this->addSql('ALTER TABLE programming_document ADD CONSTRAINT FK_CB97423AE83BB93A FOREIGN KEY (programming_document_type_id) REFERENCES programming_document_type (id)');
        $this->addSql('ALTER TABLE programming_document ADD CONSTRAINT FK_CB97423A7E3C61F9 FOREIGN KEY (owner_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE programming_document ADD CONSTRAINT FK_CB97423A166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE programming_document ADD CONSTRAINT FK_CB97423A93CB796C FOREIGN KEY (file_id) REFERENCES programming_document_file (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE programming_document DROP FOREIGN KEY FK_CB97423A93CB796C');
        $this->addSql('ALTER TABLE programming_document_file DROP FOREIGN KEY FK_DB64D66AE3998EDD');
        $this->addSql('ALTER TABLE programming_document DROP FOREIGN KEY FK_CB97423AE83BB93A');
        $this->addSql('DROP TABLE programming_document_file');
        $this->addSql('DROP TABLE programming_document');
        $this->addSql('DROP TABLE programming_document_type');
    }
}
