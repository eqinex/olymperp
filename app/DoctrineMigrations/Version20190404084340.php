<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190404084340 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE supplies_category (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, parent_category_id INT DEFAULT NULL, title LONGTEXT NOT NULL, INDEX IDX_64071A7E7E3C61F9 (owner_id), INDEX IDX_64071A7E796A8F92 (parent_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE supplier_supplies_category (supplier_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_A9871D5B2ADD6D8C (supplier_id), INDEX IDX_A9871D5B12469DE2 (category_id), PRIMARY KEY(supplier_id, category_id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE supplies_category ADD CONSTRAINT FK_64071A7E7E3C61F9 FOREIGN KEY (owner_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE supplies_category ADD CONSTRAINT FK_64071A7E796A8F92 FOREIGN KEY (parent_category_id) REFERENCES supplies_category (id)');
        $this->addSql('ALTER TABLE supplier_supplies_category ADD CONSTRAINT FK_A9871D5B2ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id)');
        $this->addSql('ALTER TABLE supplier_supplies_category ADD CONSTRAINT FK_A9871D5B12469DE2 FOREIGN KEY (category_id) REFERENCES supplies_category (id)');
        $this->addSql('ALTER TABLE activity CHANGE result result INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE supplies_category DROP FOREIGN KEY FK_64071A7E796A8F92');
        $this->addSql('ALTER TABLE supplier_supplies_category DROP FOREIGN KEY FK_A9871D5B12469DE2');
        $this->addSql('DROP TABLE supplies_category');
        $this->addSql('DROP TABLE supplier_supplies_category');
        $this->addSql('ALTER TABLE activity CHANGE result result VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
