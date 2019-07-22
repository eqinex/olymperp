<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190410093609 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_906517442DA68207 ON invoice');
        $this->addSql('ALTER TABLE invoice ADD request_id INT DEFAULT NULL, ADD created_at DATETIME NOT NULL, ADD amount INT DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744427EB8A5 FOREIGN KEY (request_id) REFERENCES purchase_request (id)');
        $this->addSql('CREATE INDEX IDX_90651744427EB8A5 ON invoice (request_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_90651744427EB8A5');
        $this->addSql('DROP INDEX IDX_90651744427EB8A5 ON invoice');
        $this->addSql('ALTER TABLE invoice DROP request_id, DROP created_at, DROP amount');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_906517442DA68207 ON invoice (invoice_number)');
    }
}
