<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180807050632 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE purchase_request ADD project_leader_id INT DEFAULT NULL, ADD project_leader_approved TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE purchase_request ADD CONSTRAINT FK_204D45E662290B03 FOREIGN KEY (project_leader_id) REFERENCES fos_user_user (id)');
        $this->addSql('CREATE INDEX idx_project_leader_id ON purchase_request (project_leader_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE purchase_request DROP FOREIGN KEY FK_204D45E662290B03');
        $this->addSql('DROP INDEX idx_project_leader_id ON purchase_request');
        $this->addSql('ALTER TABLE purchase_request DROP project_leader_id, DROP project_leader_approved');
    }
}
