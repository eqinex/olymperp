<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190430052315 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE applicant_status (id INT AUTO_INCREMENT NOT NULL, status LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE applicant ADD status_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE applicant ADD CONSTRAINT FK_CAAD10197B00651C FOREIGN KEY (status_id) REFERENCES applicant_status (id)');
        $this->addSql('CREATE INDEX IDX_CAAD10197B00651C ON applicant (status_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE applicant DROP FOREIGN KEY FK_CAAD10197B00651C');
        $this->addSql('DROP TABLE applicant_status');
        $this->addSql('DROP INDEX IDX_CAAD10197B00651C ON applicant');
        $this->addSql('ALTER TABLE applicant DROP status_id');
    }
}
