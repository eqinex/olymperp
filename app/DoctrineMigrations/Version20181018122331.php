<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181018122331 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE task_results (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE project_task ADD result INT DEFAULT NULL, ADD subject VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE project_task ADD CONSTRAINT FK_6BEF133D136AC113 FOREIGN KEY (result) REFERENCES task_results (id)');
        $this->addSql('CREATE INDEX IDX_6BEF133D136AC113 ON project_task (result)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE project_task DROP FOREIGN KEY FK_6BEF133D136AC113');
        $this->addSql('DROP TABLE task_results');
        $this->addSql('DROP INDEX IDX_6BEF133D136AC113 ON project_task');
        $this->addSql('ALTER TABLE project_task DROP result, DROP subject');
    }
}
