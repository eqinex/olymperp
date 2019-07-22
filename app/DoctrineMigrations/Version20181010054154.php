<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181010054154 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE engineering_document (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, project_id INT DEFAULT NULL, inventory_number VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, designation VARCHAR(255) NOT NULL, number_of_pages VARCHAR(255) DEFAULT NULL, format VARCHAR(255) DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, code VARCHAR(255) NOT NULL, class VARCHAR(255) NOT NULL, subgroup VARCHAR(255) NOT NULL, index_number VARCHAR(255) NOT NULL, document_execution VARCHAR(255) DEFAULT NULL, decryption_code LONGTEXT DEFAULT NULL, INDEX IDX_CE72A11B7E3C61F9 (owner_id), INDEX IDX_CE72A11B166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE engineering_document ADD CONSTRAINT FK_CE72A11B7E3C61F9 FOREIGN KEY (owner_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE engineering_document ADD CONSTRAINT FK_CE72A11B166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE engineering_document');
    }
}
