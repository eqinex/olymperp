<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190718085916 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE project_code DROP FOREIGN KEY FK_4E84F880979B1AD6');
        $this->addSql('CREATE TABLE company_code (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE company');
        $this->addSql('ALTER TABLE project_code DROP FOREIGN KEY FK_4E84F880166D1F9C');
        $this->addSql('DROP INDEX IDX_4E84F880166D1F9C ON project_code');
        $this->addSql('DROP INDEX IDX_4E84F880979B1AD6 ON project_code');
        $this->addSql('ALTER TABLE project_code ADD company_code_id INT DEFAULT NULL, ADD project_number VARCHAR(255) NOT NULL, ADD reserve_responsible VARCHAR(255) DEFAULT NULL, ADD inside_code VARCHAR(255) DEFAULT NULL, ADD project_location VARCHAR(255) DEFAULT NULL, DROP project_id, DROP company_id, CHANGE kit_engineering_document kit_engineering_document VARCHAR(255) DEFAULT NULL, CHANGE project_structure project_structure VARCHAR(255) DEFAULT NULL, CHANGE owner_id code VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE project_code ADD CONSTRAINT FK_4E84F880788E4C0 FOREIGN KEY (company_code_id) REFERENCES company_code (id)');
        $this->addSql('CREATE INDEX IDX_4E84F880788E4C0 ON project_code (company_code_id)');

        $companyCodes = [
            'АТ',
            'УБ',
            'ВМИ',
            'БПАГ',
            'ЧП',
            'МП'
        ];

        foreach ($companyCodes as $companyCode) {
            $this->addSql("INSERT INTO company_code (name) VALUES ('" . $companyCode . "')");
        }

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE project_code DROP FOREIGN KEY FK_4E84F880788E4C0');
        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, title LONGTEXT NOT NULL COLLATE utf8_unicode_ci, short_title VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, code VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE company_code');
        $this->addSql('DROP INDEX IDX_4E84F880788E4C0 ON project_code');
        $this->addSql('ALTER TABLE project_code ADD company_id INT DEFAULT NULL, ADD owner_id VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, DROP project_number, DROP code, DROP reserve_responsible, DROP inside_code, DROP project_location, CHANGE kit_engineering_document kit_engineering_document INT DEFAULT NULL, CHANGE project_structure project_structure INT DEFAULT NULL, CHANGE company_code_id project_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE project_code ADD CONSTRAINT FK_4E84F880166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE project_code ADD CONSTRAINT FK_4E84F880979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('CREATE INDEX IDX_4E84F880166D1F9C ON project_code (project_id)');
        $this->addSql('CREATE INDEX IDX_4E84F880979B1AD6 ON project_code (company_id)');
    }
}
