<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190109062249 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE technical_map_comment (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, technical_map_id INT DEFAULT NULL, parent_comment_id INT DEFAULT NULL, created_at DATETIME NOT NULL, comment_text LONGTEXT NOT NULL, INDEX IDX_D9EA313E7E3C61F9 (owner_id), INDEX IDX_D9EA313E19F50330 (technical_map_id), INDEX IDX_D9EA313EBF2AF943 (parent_comment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE technical_map_comment ADD CONSTRAINT FK_D9EA313E7E3C61F9 FOREIGN KEY (owner_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE technical_map_comment ADD CONSTRAINT FK_D9EA313E19F50330 FOREIGN KEY (technical_map_id) REFERENCES technical_map (id)');
        $this->addSql('ALTER TABLE technical_map_comment ADD CONSTRAINT FK_D9EA313EBF2AF943 FOREIGN KEY (parent_comment_id) REFERENCES technical_map_comment (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE technical_map_comment DROP FOREIGN KEY FK_D9EA313EBF2AF943');
        $this->addSql('DROP TABLE technical_map_comment');
    }
}
