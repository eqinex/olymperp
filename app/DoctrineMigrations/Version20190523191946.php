<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190523191946 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE manager_stats (id INT AUTO_INCREMENT NOT NULL, manager_id INT DEFAULT NULL, assigned_requests INT NOT NULL, requests_in_progress INT NOT NULL, requests_processed INT NOT NULL, items_processed INT NOT NULL, processed_prices_amount DOUBLE PRECISION NOT NULL, finished_requests INT NOT NULL, created_at DATETIME NOT NULL, stats_date DATETIME NOT NULL, INDEX IDX_7E98A790783E3463 (manager_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE manager_stats ADD CONSTRAINT FK_7E98A790783E3463 FOREIGN KEY (manager_id) REFERENCES fos_user_user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE manager_stats');
    }
}
