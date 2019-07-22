<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190207055836 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE serial (id INT AUTO_INCREMENT NOT NULL, serial_category_id INT DEFAULT NULL, ware_id INT DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_D374C9DC1D811D9A (serial_category_id), INDEX IDX_D374C9DC61E14598 (ware_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE serial_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE serial ADD CONSTRAINT FK_D374C9DC1D811D9A FOREIGN KEY (serial_category_id) REFERENCES serial_category (id)');
        $this->addSql('ALTER TABLE serial ADD CONSTRAINT FK_D374C9DC61E14598 FOREIGN KEY (ware_id) REFERENCES ware (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE serial DROP FOREIGN KEY FK_D374C9DC1D811D9A');
        $this->addSql('DROP TABLE serial');
        $this->addSql('DROP TABLE serial_category');
    }
}
