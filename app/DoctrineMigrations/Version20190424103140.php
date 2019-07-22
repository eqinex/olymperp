<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190424103140 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE project_code ADD created_year VARCHAR(255) DEFAULT NULL, ADD company_id INT DEFAULT NULL, ADD responsible_id INT DEFAULT NULL, DROP organization, ADD deleted TINYINT(1) DEFAULT NULL, CHANGE created_at date_of_registration DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE project_code ADD CONSTRAINT FK_4E84F880979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE project_code ADD CONSTRAINT FK_4E84F880602AD315 FOREIGN KEY (responsible_id) REFERENCES fos_user_user (id)');
        $this->addSql('CREATE INDEX IDX_4E84F880979B1AD6 ON project_code (company_id)');
        $this->addSql('CREATE INDEX IDX_4E84F880602AD315 ON project_code (responsible_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE project_code DROP FOREIGN KEY FK_4E84F880979B1AD6');
        $this->addSql('ALTER TABLE project_code DROP FOREIGN KEY FK_4E84F880602AD315');
        $this->addSql('DROP INDEX IDX_4E84F880979B1AD6 ON project_code');
        $this->addSql('DROP INDEX IDX_4E84F880602AD315 ON project_code');
        $this->addSql('ALTER TABLE project_code DROP created_year, DROP company_id, DROP responsible_id, ADD organization VARCHAR(255) DEFAULT NULL, DROP deleted, CHANGE date_of_registration created_at DATETIME DEFAULT NULL');
    }
}
