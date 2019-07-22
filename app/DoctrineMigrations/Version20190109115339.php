<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190109115339 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE technical_map_signatory (id INT AUTO_INCREMENT NOT NULL, technical_map_id INT DEFAULT NULL, signatory_id INT DEFAULT NULL, approved TINYINT(1) NOT NULL, INDEX IDX_F70E390219F50330 (technical_map_id), INDEX IDX_F70E390255E3D1E0 (signatory_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE technical_map_signatory ADD CONSTRAINT FK_F70E390219F50330 FOREIGN KEY (technical_map_id) REFERENCES technical_map (id)');
        $this->addSql('ALTER TABLE technical_map_signatory ADD CONSTRAINT FK_F70E390255E3D1E0 FOREIGN KEY (signatory_id) REFERENCES fos_user_user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE technical_map_signatory');
    }
}
