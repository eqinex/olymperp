<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190404054927 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE acl_file (file_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_AB93570393CB796C (file_id), INDEX IDX_AB935703A76ED395 (user_id), PRIMARY KEY(file_id, user_id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE acl_file ADD CONSTRAINT FK_AB93570393CB796C FOREIGN KEY (file_id) REFERENCES project_file (id)');
        $this->addSql('ALTER TABLE acl_file ADD CONSTRAINT FK_AB935703A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE project_file ADD full_access TINYINT(1) NOT NULL');
        $this->addSql('INSERT INTO acl_file (file_id, user_id)
                            SELECT pf.id, u.id
                            FROM project_file as pf
                                INNER JOIN project_task as pt ON pf.task_id = pt.id
                                INNER JOIN fos_user_user as u ON pt.responsible_user_id = u.id
                            WHERE pf.full_access = 0 and pf.owner_id != u.id
                            UNION
                            SELECT pf.id, u.id
                            FROM project_file as pf
                                INNER JOIN project_task as pt ON pf.task_id = pt.id
                                INNER JOIN fos_user_user as u ON pt.controlling_user_id = u.id
                            WHERE pf.full_access = 0 and pf.owner_id != u.id 
                            UNION
                            SELECT pf.id, u.id
                            FROM project_file as pf
                                INNER JOIN project as pr ON pf.project_id = pr.id
                                INNER JOIN fos_user_user as u ON pr.leader_id = u.id
                            WHERE pf.full_access = 0 and pf.owner_id != u.id 
                            UNION
                            SELECT pf.id, us.id
                            FROM project_file as pf
                                INNER JOIN fos_user_user as u ON pf.owner_id = u.id
                                INNER JOIN team as t ON u.team_id = t.id
                                INNER JOIN fos_user_user as us ON t.leader_id = us.id
                            WHERE pf.full_access = 0 and pf.owner_id != us.id'
        );
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE acl_file');
        $this->addSql('ALTER TABLE project_file DROP full_access');
    }
}
