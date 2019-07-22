<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190111050947 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE technical_map_file (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, technical_map_id INT DEFAULT NULL, file_size VARCHAR(255) NOT NULL, format VARCHAR(255) NOT NULL, uploaded_at DATETIME NOT NULL, deleted TINYINT(1) DEFAULT NULL, file_name LONGTEXT NOT NULL, stored_file_name LONGTEXT NOT NULL, stored_preview_file_name LONGTEXT DEFAULT NULL, stored_file_dir LONGTEXT DEFAULT NULL, INDEX IDX_A3BD5E3A7E3C61F9 (owner_id), INDEX IDX_A3BD5E3A19F50330 (technical_map_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE technical_map_file ADD CONSTRAINT FK_A3BD5E3A7E3C61F9 FOREIGN KEY (owner_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE technical_map_file ADD CONSTRAINT FK_A3BD5E3A19F50330 FOREIGN KEY (technical_map_id) REFERENCES technical_map (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE technical_map_file');
    }
}
