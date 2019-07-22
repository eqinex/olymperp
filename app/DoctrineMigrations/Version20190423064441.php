<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190423064441 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE document_template ADD is_basic TINYINT(1) DEFAULT \'1\' NOT NULL');
        $this->addSql('ALTER TABLE document ADD document_template_supplementary_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A7657B25196 FOREIGN KEY (document_template_supplementary_id) REFERENCES document_template (id)');
        $this->addSql('CREATE INDEX IDX_D8698A7657B25196 ON document (document_template_supplementary_id)');
        $this->addSql('INSERT INTO document_template (title, code, is_basic) VALUES ("Дополнительное соглашение", "ДС", false)');
        $this->addSql('INSERT INTO document_template (title, code, is_basic) VALUES ("Спецификация", "СПЦ", false)');
        $this->addSql('INSERT INTO document_template (title, code, is_basic) VALUES ("Протокол разногласий", "ПРЗ", false)');
        $this->addSql('INSERT INTO document_template (title, code, is_basic) VALUES ("Универсальный передаточный документ", "УПД", false)');
        $this->addSql('INSERT INTO document_template (title, code, is_basic) VALUES ("Счет-фактура", "СФ", false)');
        $this->addSql('INSERT INTO document_template (title, code, is_basic) VALUES ("Счет", "СЧ", false)');
        $this->addSql('INSERT INTO document_template (title, code, is_basic) VALUES ("Накладная", "ТН", false)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE document_template DROP is_basic');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A7657B25196');
        $this->addSql('DROP INDEX IDX_D8698A7657B25196 ON document');
        $this->addSql('ALTER TABLE document DROP document_template_supplementary_id');
        $this->addSql('DELETE FROM document_template WHERE title = "Дополнительное соглашение"');
        $this->addSql('DELETE FROM document_template WHERE title = "Спецификация"');
        $this->addSql('DELETE FROM document_template WHERE title = "Протокол разногласий"');
        $this->addSql('DELETE FROM document_template WHERE title = "Универсальный передаточный документ"');
        $this->addSql('DELETE FROM document_template WHERE title = "Счет-фактура"');
        $this->addSql('DELETE FROM document_template WHERE title = "Счет"');
        $this->addSql('DELETE FROM document_template WHERE title = "Накладная"');
    }
}