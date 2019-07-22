<?php

namespace Application\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180821060033 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE purchase_request ADD ware_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE purchase_request ADD CONSTRAINT FK_204D45E661E14598 FOREIGN KEY (ware_id) REFERENCES ware (id)');
        $this->addSql('CREATE INDEX IDX_204D45E661E14598 ON purchase_request (ware_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE purchase_request DROP FOREIGN KEY FK_204D45E661E14598');
        $this->addSql('DROP INDEX IDX_204D45E661E14598 ON purchase_request');
        $this->addSql('ALTER TABLE purchase_request DROP ware_id');
    }
}
