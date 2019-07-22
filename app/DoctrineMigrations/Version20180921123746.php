<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180921123746 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE project_task ADD protocol_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE project_task ADD CONSTRAINT FK_6BEF133DCCD59258 FOREIGN KEY (protocol_id) REFERENCES project_task (id)');
        $this->addSql('CREATE INDEX IDX_6BEF133DCCD59258 ON project_task (protocol_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE project_task DROP FOREIGN KEY FK_6BEF133DCCD59258');
        $this->addSql('DROP INDEX IDX_6BEF133DCCD59258 ON project_task');
        $this->addSql('ALTER TABLE project_task DROP protocol_id');
    }
}
