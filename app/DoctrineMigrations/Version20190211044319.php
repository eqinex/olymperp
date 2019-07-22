<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190211044319 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE serial_items (serial_id INT NOT NULL, item_id INT NOT NULL, INDEX IDX_814DEF8DAF82D095 (serial_id), INDEX IDX_814DEF8D126F525E (item_id), PRIMARY KEY(serial_id, item_id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE serial_items ADD CONSTRAINT FK_814DEF8DAF82D095 FOREIGN KEY (serial_id) REFERENCES serial (id)');
        $this->addSql('ALTER TABLE serial_items ADD CONSTRAINT FK_814DEF8D126F525E FOREIGN KEY (item_id) REFERENCES purchase_request_item (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE serial_items');
    }
}
