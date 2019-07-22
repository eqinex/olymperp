<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190411070303 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE task_file_download_manager (id INT AUTO_INCREMENT NOT NULL, task_file_id INT DEFAULT NULL, user_id INT DEFAULT NULL, download_date DATETIME NOT NULL, INDEX IDX_72EDE1D8C2A5386 (task_file_id), INDEX IDX_72EDE1DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE task_file_download_manager ADD CONSTRAINT FK_72EDE1D8C2A5386 FOREIGN KEY (task_file_id) REFERENCES project_file (id)');
        $this->addSql('ALTER TABLE task_file_download_manager ADD CONSTRAINT FK_72EDE1DA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE task_file_download_manager');
    }
}
