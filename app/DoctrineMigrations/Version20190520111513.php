<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190520111513 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE tenant (id INT AUTO_INCREMENT NOT NULL, supplier_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, full_title VARCHAR(255) DEFAULT NULL, legal_address VARCHAR(255) DEFAULT NULL, actual_address VARCHAR(255) DEFAULT NULL, postal_address VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, site VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, fax VARCHAR(255) DEFAULT NULL, itn VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_4E59C4622B36786B (title), UNIQUE INDEX UNIQ_4E59C462591ED910 (itn), INDEX IDX_4E59C4622ADD6D8C (supplier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rent (id INT AUTO_INCREMENT NOT NULL, tenant_id INT DEFAULT NULL, employee_id INT DEFAULT NULL, rent DOUBLE PRECISION NOT NULL, heating DOUBLE PRECISION NOT NULL, communal_payments DOUBLE PRECISION NOT NULL, total DOUBLE PRECISION NOT NULL, square DOUBLE PRECISION NOT NULL, payment_method VARCHAR(255) NOT NULL, date DATETIME NOT NULL, INDEX IDX_2784DCC9033212A (tenant_id), INDEX IDX_2784DCC8C03F15C (employee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tenant ADD CONSTRAINT FK_4E59C4622ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id)');
        $this->addSql('ALTER TABLE rent ADD CONSTRAINT FK_2784DCC9033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id)');
        $this->addSql('ALTER TABLE rent ADD CONSTRAINT FK_2784DCC8C03F15C FOREIGN KEY (employee_id) REFERENCES fos_user_user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE rent DROP FOREIGN KEY FK_2784DCC9033212A');
        $this->addSql('DROP TABLE tenant');
        $this->addSql('DROP TABLE rent');
    }
}
