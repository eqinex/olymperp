<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190205092614 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE invoice (id INT AUTO_INCREMENT NOT NULL, supplier_id INT DEFAULT NULL, invoice_number VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_906517442DA68207 (invoice_number), INDEX IDX_906517442ADD6D8C (supplier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_906517442ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id)');
        $this->addSql('ALTER TABLE purchase_request_item ADD invoice_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE purchase_request_item ADD CONSTRAINT FK_A4A6F5442989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id)');
        $this->addSql('CREATE INDEX IDX_A4A6F5442989F1FD ON purchase_request_item (invoice_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE purchase_request_item DROP FOREIGN KEY FK_A4A6F5442989F1FD');
        $this->addSql('DROP TABLE invoice');
        $this->addSql('DROP INDEX IDX_A4A6F5442989F1FD ON purchase_request_item');
        $this->addSql('ALTER TABLE purchase_request_item DROP invoice_id');
    }
}
