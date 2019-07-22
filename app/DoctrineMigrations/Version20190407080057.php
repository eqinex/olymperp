<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190407080057 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE purchase_request_item ADD supplies_category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE purchase_request_item ADD CONSTRAINT FK_A4A6F5441667EF76 FOREIGN KEY (supplies_category_id) REFERENCES supplies_category (id)');
        $this->addSql('CREATE INDEX IDX_A4A6F5441667EF76 ON purchase_request_item (supplies_category_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE purchase_request_item DROP FOREIGN KEY FK_A4A6F5441667EF76');
        $this->addSql('DROP INDEX IDX_A4A6F5441667EF76 ON purchase_request_item');
        $this->addSql('ALTER TABLE purchase_request_item DROP supplies_category_id');
    }
}
