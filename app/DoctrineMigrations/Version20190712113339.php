<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190712113339 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE medical_institution_category (id INT AUTO_INCREMENT NOT NULL, name LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE medical_institution (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, name LONGTEXT NOT NULL, type VARCHAR(255) DEFAULT NULL, region VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, district VARCHAR(255) DEFAULT NULL, index_medical_institution VARCHAR(255) DEFAULT NULL, address LONGTEXT DEFAULT NULL, phone LONGTEXT DEFAULT NULL, email LONGTEXT DEFAULT NULL, site LONGTEXT DEFAULT NULL, INDEX IDX_A866575712469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE medical_institution ADD CONSTRAINT FK_A866575712469DE2 FOREIGN KEY (category_id) REFERENCES medical_institution_category (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE medical_institution DROP FOREIGN KEY FK_A866575712469DE2');
        $this->addSql('DROP TABLE medical_institution_category');
        $this->addSql('DROP TABLE medical_institution');
    }
}
