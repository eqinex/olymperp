<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190517115640 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE vacancy ADD employee_role_id INT DEFAULT NULL, ADD team_id INT DEFAULT NULL, DROP salary, DROP title');
        $this->addSql('ALTER TABLE vacancy ADD CONSTRAINT FK_A9346CBD564F74A3 FOREIGN KEY (employee_role_id) REFERENCES project_role (id)');
        $this->addSql('ALTER TABLE vacancy ADD CONSTRAINT FK_A9346CBD296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('CREATE INDEX IDX_A9346CBD564F74A3 ON vacancy (employee_role_id)');
        $this->addSql('CREATE INDEX IDX_A9346CBD296CD8AE ON vacancy (team_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE vacancy DROP FOREIGN KEY FK_A9346CBD564F74A3');
        $this->addSql('ALTER TABLE vacancy DROP FOREIGN KEY FK_A9346CBD296CD8AE');
        $this->addSql('DROP INDEX IDX_A9346CBD564F74A3 ON vacancy');
        $this->addSql('DROP INDEX IDX_A9346CBD296CD8AE ON vacancy');
        $this->addSql('ALTER TABLE vacancy ADD salary INT NOT NULL, ADD title LONGTEXT NOT NULL COLLATE utf8_unicode_ci, DROP employee_role_id, DROP team_id');
    }
}
