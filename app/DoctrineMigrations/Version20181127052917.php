<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181127052917 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE activity (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, project_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, activity VARCHAR(255) NOT NULL, profitability VARCHAR(255) NOT NULL, plan DOUBLE PRECISION DEFAULT NULL, fact DOUBLE PRECISION DEFAULT NULL, residue DOUBLE PRECISION DEFAULT NULL, result VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_AC74095A77153098 (code), INDEX IDX_AC74095A7E3C61F9 (owner_id), INDEX IDX_AC74095A166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE activity_events (id INT AUTO_INCREMENT NOT NULL, activity_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_1F1B74A81C06096 (activity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A7E3C61F9 FOREIGN KEY (owner_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE activity_events ADD CONSTRAINT FK_1F1B74A81C06096 FOREIGN KEY (activity_id) REFERENCES activity (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE activity_events DROP FOREIGN KEY FK_1F1B74A81C06096');
        $this->addSql('DROP TABLE activity');
        $this->addSql('DROP TABLE activity_events');
    }
}
