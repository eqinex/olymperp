<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190116061815 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('INSERT INTO programming_document_type (name) VALUES ("Спецификация")');
        $this->addSql('INSERT INTO programming_document_type (name, code) VALUES ("Ведомость держателей подлинников", "05")');
        $this->addSql('INSERT INTO programming_document_type (name, code) VALUES ("Текст программы", "12")');
        $this->addSql('INSERT INTO programming_document_type (name, code) VALUES ("Описание программы", "13")');
        $this->addSql('INSERT INTO programming_document_type (name, code) VALUES ("Ведомость эксплуатационных документов", "20")');
        $this->addSql('INSERT INTO programming_document_type (name, code) VALUES ("Формуляр", "30")');
        $this->addSql('INSERT INTO programming_document_type (name, code) VALUES ("Описание применения", "31")');
        $this->addSql('INSERT INTO programming_document_type (name, code) VALUES ("Руководство системного программиста", "32")');
        $this->addSql('INSERT INTO programming_document_type (name, code) VALUES ("Руководство программиста", "33")');
        $this->addSql('INSERT INTO programming_document_type (name, code) VALUES ("Руководство оператора", "34")');
        $this->addSql('INSERT INTO programming_document_type (name, code) VALUES ("Описание языка", "35")');
        $this->addSql('INSERT INTO programming_document_type (name, code) VALUES ("Руководство по техническому обслуживанию", "46")');
        $this->addSql('INSERT INTO programming_document_type (name, code) VALUES ("Программа и методика испытаний", "51")');
        $this->addSql('INSERT INTO programming_document_type (name, code) VALUES ("Пояснительная записка", "81")');
        $this->addSql('INSERT INTO programming_document_type (name, code) VALUES ("Прочие документы", "90-99")');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DELETE FROM programming_document_type WHERE name = "Спецификация"');
        $this->addSql('DELETE FROM programming_document_type WHERE name = "Ведомость держателей подлинников"');
        $this->addSql('DELETE FROM programming_document_type WHERE name = "Текст программы"');
        $this->addSql('DELETE FROM programming_document_type WHERE name = "Описание программы"');
        $this->addSql('DELETE FROM programming_document_type WHERE name = "Ведомость эксплуатационных документов"');
        $this->addSql('DELETE FROM programming_document_type WHERE name = "Формуляр"');
        $this->addSql('DELETE FROM programming_document_type WHERE name = "Описание применения"');
        $this->addSql('DELETE FROM programming_document_type WHERE name = "Руководство системного программиста"');
        $this->addSql('DELETE FROM programming_document_type WHERE name = "Руководство программиста"');
        $this->addSql('DELETE FROM programming_document_type WHERE name = "Руководство оператора"');
        $this->addSql('DELETE FROM programming_document_type WHERE name = "Описание языка"');
        $this->addSql('DELETE FROM programming_document_type WHERE name = "Руководство по техническому обслуживанию"');
        $this->addSql('DELETE FROM programming_document_type WHERE name = "Программа и методика испытаний"');
        $this->addSql('DELETE FROM programming_document_type WHERE name = "Пояснительная записка"');
        $this->addSql('DELETE FROM programming_document_type WHERE name = "Прочие документы"');
    }
}
