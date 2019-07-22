<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181128100855 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE activity ADD responsible_user_id INT DEFAULT NULL, DROP residue, CHANGE code code VARCHAR(255) DEFAULT NULL, CHANGE activity activity VARCHAR(255) DEFAULT NULL, CHANGE result result VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095ABDAD1998 FOREIGN KEY (responsible_user_id) REFERENCES fos_user_user (id)');
        $this->addSql('CREATE INDEX IDX_AC74095ABDAD1998 ON activity (responsible_user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095ABDAD1998');
        $this->addSql('DROP INDEX IDX_AC74095ABDAD1998 ON activity');
        $this->addSql('ALTER TABLE activity ADD residue DOUBLE PRECISION DEFAULT NULL, DROP responsible_user_id, CHANGE code code VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE activity activity VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE result result VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
    }
}
