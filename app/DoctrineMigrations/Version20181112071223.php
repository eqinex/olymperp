<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181112071223 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE engineering_document_file (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, engineering_document_id INT DEFAULT NULL, file_size VARCHAR(255) NOT NULL, format VARCHAR(255) NOT NULL, uploaded_at DATETIME NOT NULL, file_name LONGTEXT NOT NULL, stored_file_dir LONGTEXT DEFAULT NULL, INDEX IDX_3151D8067E3C61F9 (owner_id), UNIQUE INDEX UNIQ_3151D8067F2514D7 (engineering_document_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE engineering_document_file ADD CONSTRAINT FK_3151D8067E3C61F9 FOREIGN KEY (owner_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE engineering_document_file ADD CONSTRAINT FK_3151D8067F2514D7 FOREIGN KEY (engineering_document_id) REFERENCES engineering_document (id)');
        $this->addSql('ALTER TABLE engineering_document ADD file_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE engineering_document ADD CONSTRAINT FK_CE72A11B93CB796C FOREIGN KEY (file_id) REFERENCES engineering_document_file (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CE72A11B93CB796C ON engineering_document (file_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE engineering_document DROP FOREIGN KEY FK_CE72A11B93CB796C');
        $this->addSql('DROP TABLE engineering_document_file');
        $this->addSql('DROP INDEX UNIQ_CE72A11B93CB796C ON engineering_document');
        $this->addSql('ALTER TABLE engineering_document DROP file_id');
    }
}
