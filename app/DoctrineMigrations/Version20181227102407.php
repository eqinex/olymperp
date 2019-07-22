<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181227102407 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE technical_map_solutions (id INT AUTO_INCREMENT NOT NULL, technical_map_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, criterion_1 VARCHAR(255) NOT NULL, points_1 INT DEFAULT NULL, criterion_2 VARCHAR(255) NOT NULL, points_2 INT DEFAULT NULL, criterion_3 VARCHAR(255) NOT NULL, points_3 INT DEFAULT NULL, criterion_4 VARCHAR(255) DEFAULT NULL, points_4 INT DEFAULT NULL, INDEX IDX_56A6B5D419F50330 (technical_map_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE technical_map (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, project_id INT DEFAULT NULL, code VARCHAR(255) DEFAULT NULL, task VARCHAR(255) NOT NULL, status INT NOT NULL, goal VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_4F7D620577153098 (code), INDEX IDX_4F7D62057E3C61F9 (owner_id), INDEX IDX_4F7D6205166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE technical_map_solutions ADD CONSTRAINT FK_56A6B5D419F50330 FOREIGN KEY (technical_map_id) REFERENCES technical_map (id)');
        $this->addSql('ALTER TABLE technical_map ADD CONSTRAINT FK_4F7D62057E3C61F9 FOREIGN KEY (owner_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE technical_map ADD CONSTRAINT FK_4F7D6205166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE technical_map_solutions DROP FOREIGN KEY FK_56A6B5D419F50330');
        $this->addSql('DROP TABLE technical_map_solutions');
        $this->addSql('DROP TABLE technical_map');
    }
}
