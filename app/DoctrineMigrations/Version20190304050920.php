<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190304050920 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE computer ADD cartridge_type VARCHAR(255) DEFAULT NULL, ADD quantity INT NOT NULL, ADD ip_address_computer VARCHAR(255) DEFAULT NULL, ADD mac_address_computer VARCHAR(255) DEFAULT NULL, CHANGE ip_address ip_address VARCHAR(255) DEFAULT NULL, CHANGE mac_address mac_address VARCHAR(255) DEFAULT NULL');
        $this->addSql('UPDATE computer SET ip_address_computer = concat(\'\["\' , ip_address , \'\"]\') WHERE ip_address <> \'\'');
        $this->addSql('UPDATE computer SET mac_address_computer = concat(\'\["\' , mac_address , \'\"]\') WHERE mac_address <> \'\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE computer DROP cartridge_type, DROP quantity, DROP ip_address_computer, DROP mac_address_computer, CHANGE ip_address ip_address VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE mac_address mac_address VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
    }
}
