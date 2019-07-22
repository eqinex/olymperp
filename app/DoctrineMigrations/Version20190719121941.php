<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190719121941 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE category_price (id INT AUTO_INCREMENT NOT NULL, price_iteration_id INT DEFAULT NULL, request_category_id INT DEFAULT NULL, price DOUBLE PRECISION DEFAULT NULL, INDEX IDX_22FB6D90EF9B8FFC (price_iteration_id), INDEX IDX_22FB6D9039BA3EB7 (request_category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_price (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, INDEX IDX_D99D2B47166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE price_iteration (id INT AUTO_INCREMENT NOT NULL, project_price_id INT DEFAULT NULL, INDEX IDX_6D60E2E1EB5A4C3C (project_price_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category_price ADD CONSTRAINT FK_22FB6D90EF9B8FFC FOREIGN KEY (price_iteration_id) REFERENCES price_iteration (id)');
        $this->addSql('ALTER TABLE category_price ADD CONSTRAINT FK_22FB6D9039BA3EB7 FOREIGN KEY (request_category_id) REFERENCES purchase_request_category (id)');
        $this->addSql('ALTER TABLE project_price ADD CONSTRAINT FK_D99D2B47166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE price_iteration ADD CONSTRAINT FK_6D60E2E1EB5A4C3C FOREIGN KEY (project_price_id) REFERENCES project_price (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE price_iteration DROP FOREIGN KEY FK_6D60E2E1EB5A4C3C');
        $this->addSql('ALTER TABLE category_price DROP FOREIGN KEY FK_22FB6D90EF9B8FFC');
        $this->addSql('DROP TABLE category_price');
        $this->addSql('DROP TABLE project_price');
        $this->addSql('DROP TABLE price_iteration');
    }
}
