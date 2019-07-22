<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190404185249 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE purchase_request ADD supplies_category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE purchase_request ADD CONSTRAINT FK_204D45E61667EF76 FOREIGN KEY (supplies_category_id) REFERENCES supplies_category (id)');
        $this->addSql('CREATE INDEX IDX_204D45E61667EF76 ON purchase_request (supplies_category_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE purchase_request DROP FOREIGN KEY FK_204D45E61667EF76');
        $this->addSql('DROP INDEX IDX_204D45E61667EF76 ON purchase_request');
        $this->addSql('ALTER TABLE purchase_request DROP supplies_category_id');
    }
}
