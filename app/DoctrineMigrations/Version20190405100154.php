<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190405100154 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE applicant ADD employee_role_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE applicant ADD CONSTRAINT FK_CAAD1019564F74A3 FOREIGN KEY (employee_role_id) REFERENCES project_role (id)');
        $this->addSql('CREATE INDEX IDX_CAAD1019564F74A3 ON applicant (employee_role_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE applicant DROP FOREIGN KEY FK_CAAD1019564F74A3');
        $this->addSql('DROP INDEX IDX_CAAD1019564F74A3 ON applicant');
        $this->addSql('ALTER TABLE applicant DROP employee_role_id');
    }
}
