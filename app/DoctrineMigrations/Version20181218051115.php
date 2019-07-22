<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181218051115 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE activity_diff (id INT AUTO_INCREMENT NOT NULL, changed_by_id INT DEFAULT NULL, activity_id INT DEFAULT NULL, field VARCHAR(255) NOT NULL, old_value LONGTEXT DEFAULT NULL, new_value LONGTEXT DEFAULT NULL, updated_at DATETIME NOT NULL, INDEX IDX_46B49C39828AD0A0 (changed_by_id), INDEX IDX_46B49C3981C06096 (activity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE activity_diff ADD CONSTRAINT FK_46B49C39828AD0A0 FOREIGN KEY (changed_by_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE activity_diff ADD CONSTRAINT FK_46B49C3981C06096 FOREIGN KEY (activity_id) REFERENCES activity (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE activity_diff');
    }
}
