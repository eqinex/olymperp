<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190122111616 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE activity SET result = "2" WHERE result != "1"');
        $this->addSql('ALTER TABLE activity_events ADD responsible_user_id INT DEFAULT NULL, ADD status INT NOT NULL, ADD end_at DATETIME DEFAULT NULL');
        $this->addSql('UPDATE activity_events SET status = "2" WHERE status != "1"');
        $this->addSql('ALTER TABLE activity_events ADD CONSTRAINT FK_1F1B74ABDAD1998 FOREIGN KEY (responsible_user_id) REFERENCES fos_user_user (id)');
        $this->addSql('CREATE INDEX IDX_1F1B74ABDAD1998 ON activity_events (responsible_user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE activity_events DROP FOREIGN KEY FK_1F1B74ABDAD1998');
        $this->addSql('DROP INDEX IDX_1F1B74ABDAD1998 ON activity_events');
        $this->addSql('ALTER TABLE activity_events DROP responsible_user_id, DROP status, DROP end_at');
    }
}
