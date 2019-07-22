<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190531095505 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE supplier_legal_form (id INT AUTO_INCREMENT NOT NULL, `key` VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1E02DE118A90ABA9 (`key`), UNIQUE INDEX UNIQ_1E02DE115E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');

        $legalForms = [
            "OOO" => "ООО",
            "OA" => "ОА",
            "OAO" => "ОАО",
            "NKO" => "НКО",
            "NPO" => "НПО",
            "PO" => "ПО",
            "IP" => "ИП",
            "ZAO" => "ЗАО",
            "Individual" => "Физ. лицо",
        ];

        foreach ($legalForms as $key => $legalForm) {
            $this->addSql("INSERT INTO supplier_legal_form (name, `key`) VALUES ('$legalForm', '$key')");
        }

        $this->addSql('ALTER TABLE supplier ADD legal_form_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE supplier ADD CONSTRAINT FK_9B2A6C7E98CD0513 FOREIGN KEY (legal_form_id) REFERENCES supplier_legal_form (id)');
        $this->addSql('CREATE INDEX IDX_9B2A6C7E98CD0513 ON supplier (legal_form_id)');

        $this->addSql('UPDATE supplier SET legal_form_id = (SELECT id FROM supplier_legal_form WHERE `key` = "OOO") WHERE (itn IS NOT NULL OR itn != "") AND (kpp IS NOT NULL OR kpp != "")');
        $this->addSql('UPDATE supplier SET legal_form_id = (SELECT id FROM supplier_legal_form WHERE `key` = "IP") WHERE (itn IS NOT NULL OR itn != "") AND (kpp IS NULL OR kpp = "")');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE supplier DROP FOREIGN KEY FK_9B2A6C7E98CD0513');
        $this->addSql('DROP TABLE supplier_legal_form');
        $this->addSql('DROP INDEX IDX_9B2A6C7E98CD0513 ON supplier');
        $this->addSql('ALTER TABLE supplier DROP legal_form_id');
    }
}