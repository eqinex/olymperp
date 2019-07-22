<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190607072331 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE supplier_blacklist (id INT AUTO_INCREMENT NOT NULL, supplier_id INT DEFAULT NULL, incident VARCHAR(255) NOT NULL, criticality VARCHAR(255) NOT NULL, added_at DATETIME NOT NULL, INDEX IDX_A125695B2ADD6D8C (supplier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE supplier_blacklist ADD CONSTRAINT FK_A125695B2ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id)');
        $this->addSql('ALTER TABLE supplier ADD added_to_blacklist TINYINT(1) DEFAULT NULL');

        $this->addSql('UPDATE supplier SET added_to_blacklist = FALSE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE supplier_blacklist');
        $this->addSql('ALTER TABLE supplier DROP added_to_blacklist');
    }
}
