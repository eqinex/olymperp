<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181005141509 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE task_siblings (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE project_task ADD task_siblings_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE project_task ADD CONSTRAINT FK_6BEF133D7559C451 FOREIGN KEY (task_siblings_id) REFERENCES task_siblings (id)');
        $this->addSql('CREATE INDEX IDX_6BEF133D7559C451 ON project_task (task_siblings_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE project_task DROP FOREIGN KEY FK_6BEF133D7559C451');
        $this->addSql('DROP TABLE task_siblings');
        $this->addSql('DROP INDEX IDX_6BEF133D7559C451 ON project_task');
        $this->addSql('ALTER TABLE project_task DROP task_siblings_id');
    }
}
