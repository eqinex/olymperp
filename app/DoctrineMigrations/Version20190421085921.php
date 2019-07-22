<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190421085921 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE invoice ADD status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE invoice ADD invoice_file_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_9065174461124FBF FOREIGN KEY (invoice_file_id) REFERENCES purchase_request_file (id)');
        $this->addSql('CREATE INDEX IDX_9065174461124FBF ON invoice (invoice_file_id)');
        $this->addSql('ALTER TABLE invoice ADD amount_paid DOUBLE PRECISION DEFAULT NULL, CHANGE amount amount DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE invoice DROP status');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_9065174461124FBF');
        $this->addSql('DROP INDEX IDX_9065174461124FBF ON invoice');
        $this->addSql('ALTER TABLE invoice DROP invoice_file_id');
        $this->addSql('ALTER TABLE invoice DROP amount_paid, CHANGE amount amount INT DEFAULT NULL');
    }
}
