<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181012090347 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE engineering_document CHANGE code code VARCHAR(155) NOT NULL, CHANGE class class VARCHAR(155) NOT NULL, CHANGE subgroup subgroup VARCHAR(155) NOT NULL, CHANGE index_number index_number VARCHAR(155) NOT NULL, CHANGE document_execution document_execution VARCHAR(155) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX engineering_document_unique ON engineering_document (code, class, subgroup, index_number, document_execution)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX engineering_document_unique ON engineering_document');
        $this->addSql('ALTER TABLE engineering_document CHANGE code code VARCHAR(155) NOT NULL COLLATE utf8_unicode_ci, CHANGE class class VARCHAR(155) NOT NULL COLLATE utf8_unicode_ci, CHANGE subgroup subgroup VARCHAR(155) NOT NULL COLLATE utf8_unicode_ci, CHANGE index_number index_number VARCHAR(155) NOT NULL COLLATE utf8_unicode_ci, CHANGE document_execution document_execution VARCHAR(155) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
