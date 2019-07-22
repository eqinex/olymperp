<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190529095555 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE purchase_request_favorite (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, purchase_request_id INT DEFAULT NULL, INDEX IDX_4C24EEDEA76ED395 (user_id), INDEX IDX_4C24EEDE4E4DEF6F (purchase_request_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE purchase_request_favorite ADD CONSTRAINT FK_4C24EEDEA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE purchase_request_favorite ADD CONSTRAINT FK_4C24EEDE4E4DEF6F FOREIGN KEY (purchase_request_id) REFERENCES purchase_request (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE purchase_request_favorite');
    }
}
