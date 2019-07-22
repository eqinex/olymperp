<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190508055649 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Deleted uniq title supplier, add column one_s_unique_code';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_9B2A6C7E2B36786B ON supplier');
        $this->addSql('ALTER TABLE supplier ADD one_s_unique_code VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9B2A6C7EF6A60506 ON supplier (one_s_unique_code)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_9B2A6C7EF6A60506 ON supplier');
        $this->addSql('ALTER TABLE supplier DROP one_s_unique_code');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9B2A6C7E2B36786B ON supplier (title)');
    }
}
