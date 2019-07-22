<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190114095851 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE technical_map ADD criterion_title_1 VARCHAR(255) NOT NULL, ADD max_points_1 INT DEFAULT NULL, ADD criterion_title_2 VARCHAR(255) NOT NULL, ADD max_points_2 INT DEFAULT NULL, ADD criterion_title_3 VARCHAR(255) DEFAULT NULL, ADD max_points_3 INT DEFAULT NULL, ADD criterion_title_4 VARCHAR(255) DEFAULT NULL, ADD max_points_4 INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE technical_map DROP criterion_title_1, DROP max_points_1, DROP criterion_title_2, DROP max_points_2, DROP criterion_title_3, DROP max_points_3, DROP criterion_title_4, DROP max_points_4');
    }
}
