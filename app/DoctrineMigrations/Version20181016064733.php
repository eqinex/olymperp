<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181016064733 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE protocol_members (id INT AUTO_INCREMENT NOT NULL, protocol_id INT DEFAULT NULL, member_id INT DEFAULT NULL, INDEX IDX_60827C5ACCD59258 (protocol_id), INDEX IDX_60827C5A7597D3FE (member_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE protocol_members ADD CONSTRAINT FK_60827C5ACCD59258 FOREIGN KEY (protocol_id) REFERENCES project_task (id)');
        $this->addSql('ALTER TABLE protocol_members ADD CONSTRAINT FK_60827C5A7597D3FE FOREIGN KEY (member_id) REFERENCES fos_user_user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE protocol_members');
    }
}
