<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190401122102 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE project_code (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, organization VARCHAR(255) DEFAULT NULL, project_stage VARCHAR(255) DEFAULT NULL, subassembly VARCHAR(255) DEFAULT NULL, execution VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, owner_id VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, kit_engineering_document INT DEFAULT NULL, project_structure INT DEFAULT NULL, remark VARCHAR(255) DEFAULT NULL, INDEX IDX_4E84F880166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE project_code ADD CONSTRAINT FK_4E84F880166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE project_code');
    }
}
