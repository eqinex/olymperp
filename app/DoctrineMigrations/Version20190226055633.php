<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190226055633 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE computer_parts (id INT AUTO_INCREMENT NOT NULL, computer_id INT DEFAULT NULL, part_id INT DEFAULT NULL, INDEX IDX_4C32BA52A426D518 (computer_id), INDEX IDX_4C32BA524CE34BEC (part_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE computer_parts ADD CONSTRAINT FK_4C32BA52A426D518 FOREIGN KEY (computer_id) REFERENCES computer (id)');
        $this->addSql('ALTER TABLE computer_parts ADD CONSTRAINT FK_4C32BA524CE34BEC FOREIGN KEY (part_id) REFERENCES computer_part (id)');
        $this->addSql('ALTER TABLE computer DROP FOREIGN KEY FK_A298A7A6E1159985');
        $this->addSql('DROP INDEX IDX_A298A7A6E1159985 ON computer');
        $this->addSql('INSERT INTO computer_parts (computer_id, part_id) SELECT id, monitor FROM computer WHERE monitor IS NOT NULL');
        $this->addSql('ALTER TABLE computer DROP monitor, CHANGE ip_address ip_address VARCHAR(255) NOT NULL, CHANGE mac_address mac_address VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE computer_parts');
        $this->addSql('ALTER TABLE computer ADD monitor INT DEFAULT NULL, CHANGE ip_address ip_address LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE mac_address mac_address LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE computer ADD CONSTRAINT FK_A298A7A6E1159985 FOREIGN KEY (monitor) REFERENCES computer_part (id)');
        $this->addSql('CREATE INDEX IDX_A298A7A6E1159985 ON computer (monitor)');
    }
}
