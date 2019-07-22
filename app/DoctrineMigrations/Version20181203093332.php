<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181203093332 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE supplier_comment (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, supplier INT DEFAULT NULL, parent_comment_id INT DEFAULT NULL, created_at DATETIME NOT NULL, comment_text LONGTEXT NOT NULL, INDEX IDX_151126737E3C61F9 (owner_id), INDEX IDX_151126739B2A6C7E (supplier), INDEX IDX_15112673BF2AF943 (parent_comment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE supplier_comment ADD CONSTRAINT FK_151126737E3C61F9 FOREIGN KEY (owner_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE supplier_comment ADD CONSTRAINT FK_151126739B2A6C7E FOREIGN KEY (supplier) REFERENCES supplier (id)');
        $this->addSql('ALTER TABLE supplier_comment ADD CONSTRAINT FK_15112673BF2AF943 FOREIGN KEY (parent_comment_id) REFERENCES supplier_comment (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE supplier_comment DROP FOREIGN KEY FK_15112673BF2AF943');
        $this->addSql('DROP TABLE supplier_comment');
    }
}
