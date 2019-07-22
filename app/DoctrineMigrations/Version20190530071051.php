<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190530071051 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE interview (id INT AUTO_INCREMENT NOT NULL, vacancy_id INT DEFAULT NULL, applicant_id INT DEFAULT NULL, notice LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, start_at DATETIME DEFAULT NULL, status INT NOT NULL, INDEX IDX_CF1D3C34433B78C4 (vacancy_id), INDEX IDX_CF1D3C3497139001 (applicant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE interview ADD CONSTRAINT FK_CF1D3C34433B78C4 FOREIGN KEY (vacancy_id) REFERENCES vacancy (id)');
        $this->addSql('ALTER TABLE interview ADD CONSTRAINT FK_CF1D3C3497139001 FOREIGN KEY (applicant_id) REFERENCES applicant (id)');
        $this->addSql('ALTER TABLE vacancy ADD owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vacancy ADD CONSTRAINT FK_A9346CBD7E3C61F9 FOREIGN KEY (owner_id) REFERENCES fos_user_user (id)');
        $this->addSql('CREATE INDEX IDX_A9346CBD7E3C61F9 ON vacancy (owner_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE interview');
        $this->addSql('ALTER TABLE vacancy DROP FOREIGN KEY FK_A9346CBD7E3C61F9');
        $this->addSql('DROP INDEX IDX_A9346CBD7E3C61F9 ON vacancy');
        $this->addSql('ALTER TABLE vacancy DROP owner_id');
    }
}
