<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190523120758 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE rent DROP FOREIGN KEY FK_2784DCC9033212A');
        $this->addSql('ALTER TABLE tenement DROP FOREIGN KEY FK_DCDC604F9033212A');
        $this->addSql('DROP TABLE tenant');
        $this->addSql('DROP INDEX IDX_DCDC604F9033212A ON tenement');
        $this->addSql('ALTER TABLE tenement ADD rent DOUBLE PRECISION NOT NULL, ADD heating DOUBLE PRECISION NOT NULL, ADD communal_payments DOUBLE PRECISION NOT NULL, ADD total DOUBLE PRECISION NOT NULL, ADD square DOUBLE PRECISION NOT NULL, DROP address, CHANGE tenant_id supplier_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tenement ADD CONSTRAINT FK_DCDC604F2ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id)');
        $this->addSql('CREATE INDEX IDX_DCDC604F2ADD6D8C ON tenement (supplier_id)');
        $this->addSql('DROP INDEX IDX_2784DCC9033212A ON rent');
        $this->addSql('ALTER TABLE rent CHANGE tenant_id tenement_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE rent ADD CONSTRAINT FK_2784DCCF71FFA49 FOREIGN KEY (tenement_id) REFERENCES tenement (id)');
        $this->addSql('CREATE INDEX IDX_2784DCCF71FFA49 ON rent (tenement_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE tenant (id INT AUTO_INCREMENT NOT NULL, supplier_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, full_title VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, legal_address VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, actual_address VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, postal_address VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, email VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, site VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, phone VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, fax VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, itn VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, UNIQUE INDEX UNIQ_4E59C4622B36786B (title), UNIQUE INDEX UNIQ_4E59C462591ED910 (itn), INDEX IDX_4E59C4622ADD6D8C (supplier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tenant ADD CONSTRAINT FK_4E59C4622ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id)');
        $this->addSql('ALTER TABLE rent DROP FOREIGN KEY FK_2784DCCF71FFA49');
        $this->addSql('DROP INDEX IDX_2784DCCF71FFA49 ON rent');
        $this->addSql('ALTER TABLE rent CHANGE tenement_id tenant_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE rent ADD CONSTRAINT FK_2784DCC9033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id)');
        $this->addSql('CREATE INDEX IDX_2784DCC9033212A ON rent (tenant_id)');
        $this->addSql('ALTER TABLE tenement DROP FOREIGN KEY FK_DCDC604F2ADD6D8C');
        $this->addSql('DROP INDEX IDX_DCDC604F2ADD6D8C ON tenement');
        $this->addSql('ALTER TABLE tenement ADD address VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, DROP rent, DROP heating, DROP communal_payments, DROP total, DROP square, CHANGE supplier_id tenant_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tenement ADD CONSTRAINT FK_DCDC604F9033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id)');
        $this->addSql('CREATE INDEX IDX_DCDC604F9033212A ON tenement (tenant_id)');
    }
}
