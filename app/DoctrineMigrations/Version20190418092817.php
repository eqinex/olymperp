<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190418092817 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE engineering_document ADD classifier_code VARCHAR(255) NOT NULL');
        $this->addSql('UPDATE engineering_document SET classifier_code = concat(class, subgroup)');
        $this->addSql('DROP INDEX engineering_document_unique ON engineering_document');
        $this->addSql('CREATE UNIQUE INDEX engineering_document_unique ON engineering_document (code, classifier_code, index_number, document_execution)');
        $this->addSql('ALTER TABLE engineering_document DROP class, DROP subgroup');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE engineering_document ADD class VARCHAR(155) NOT NULL COLLATE utf8_unicode_ci, ADD subgroup VARCHAR(155) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('UPDATE engineering_document SET class = substr(classifier_code, 1, 2)');
        $this->addSql('UPDATE engineering_document SET subgroup = substr(classifier_code, 3, 4)');
        $this->addSql('DROP INDEX engineering_document_unique ON engineering_document');
        $this->addSql('ALTER TABLE engineering_document DROP classifier_code');
        $this->addSql('CREATE UNIQUE INDEX engineering_document_unique ON engineering_document (code, class, subgroup, index_number, document_execution)');
    }
}
