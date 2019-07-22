<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190201102718 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE activity_events ADD success_event_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE activity_events ADD CONSTRAINT FK_1F1B74A1F3A9CCF FOREIGN KEY (success_event_id) REFERENCES activity_events (id)');
        $this->addSql('CREATE INDEX IDX_1F1B74A1F3A9CCF ON activity_events (success_event_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE activity_events DROP FOREIGN KEY FK_1F1B74A1F3A9CCF');
        $this->addSql('DROP INDEX IDX_1F1B74A1F3A9CCF ON activity_events');
        $this->addSql('ALTER TABLE activity_events DROP success_event_id');
    }
}
