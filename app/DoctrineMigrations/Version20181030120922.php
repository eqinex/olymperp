<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181030120922 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE specification (id INT AUTO_INCREMENT NOT NULL, ware INT DEFAULT NULL, project_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, unit VARCHAR(255) NOT NULL, value_task VARCHAR(255) NOT NULL, value_inner_task VARCHAR(255) NOT NULL, difference VARCHAR(255) NOT NULL, notice LONGTEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX IDX_E3F1A9ABE68688D (ware), INDEX IDX_E3F1A9A166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE specification ADD CONSTRAINT FK_E3F1A9ABE68688D FOREIGN KEY (ware) REFERENCES ware (id)');
        $this->addSql('ALTER TABLE specification ADD CONSTRAINT FK_E3F1A9A166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE specification');
    }
}
