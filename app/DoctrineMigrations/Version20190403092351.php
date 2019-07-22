<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190403092351 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE warehouse (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, short_title VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_ECB38BFC2B36786B (title), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE purchase_request ADD warehouse_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE purchase_request ADD CONSTRAINT FK_204D45E65080ECDE FOREIGN KEY (warehouse_id) REFERENCES warehouse (id)');
        $this->addSql('CREATE INDEX IDX_204D45E65080ECDE ON purchase_request (warehouse_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE purchase_request DROP FOREIGN KEY FK_204D45E65080ECDE');
        $this->addSql('DROP TABLE warehouse');
        $this->addSql('DROP INDEX IDX_204D45E65080ECDE ON purchase_request');
        $this->addSql('ALTER TABLE purchase_request DROP warehouse_id');
    }
}
