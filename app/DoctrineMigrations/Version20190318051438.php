<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190318051438 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE monitoring_hostname (id INT AUTO_INCREMENT NOT NULL, hostname VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE monitoring ADD hostname_id INT DEFAULT NULL, DROP hostname');
        $this->addSql('ALTER TABLE monitoring ADD CONSTRAINT FK_BA4F975DAC33BABE FOREIGN KEY (hostname_id) REFERENCES monitoring_hostname (id)');
        $this->addSql('CREATE INDEX IDX_BA4F975DAC33BABE ON monitoring (hostname_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE monitoring DROP FOREIGN KEY FK_BA4F975DAC33BABE');
        $this->addSql('DROP TABLE monitoring_hostname');
        $this->addSql('DROP INDEX IDX_BA4F975DAC33BABE ON monitoring');
        $this->addSql('ALTER TABLE monitoring ADD hostname VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, DROP hostname_id');
    }
}
