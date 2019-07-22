<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190121121610 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE technical_map_subscribers (technical_map_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_E3F73E4519F50330 (technical_map_id), INDEX IDX_E3F73E45A76ED395 (user_id), PRIMARY KEY(technical_map_id, user_id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE technical_map_subscribers ADD CONSTRAINT FK_E3F73E4519F50330 FOREIGN KEY (technical_map_id) REFERENCES technical_map (id)');
        $this->addSql('ALTER TABLE technical_map_subscribers ADD CONSTRAINT FK_E3F73E45A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE technical_map_subscribers');
    }
}
