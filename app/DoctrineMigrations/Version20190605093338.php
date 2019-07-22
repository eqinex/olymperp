<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190605093338 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE person');
        $this->addSql('ALTER TABLE project ADD supplier_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE2ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE2ADD6D8C ON project (supplier_id)');

        $this->addSql('UPDATE project SET supplier_id = (SELECT supplier.id FROM supplier WHERE project.client_id = supplier.client_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE person (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL COLLATE utf8_unicode_ci, phone VARCHAR(64) NOT NULL COLLATE utf8_unicode_ci, firstname VARCHAR(64) NOT NULL COLLATE utf8_unicode_ci, middlename VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, lastname VARCHAR(64) NOT NULL COLLATE utf8_unicode_ci, role VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, INDEX IDX_34DCD17619EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD17619EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE2ADD6D8C');
        $this->addSql('DROP INDEX IDX_2FB3D0EE2ADD6D8C ON project');
        $this->addSql('ALTER TABLE project DROP supplier_id');
    }
}

