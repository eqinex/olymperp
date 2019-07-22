<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181214053026 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE computer (id INT AUTO_INCREMENT NOT NULL, employee_id INT DEFAULT NULL, operation_system INT DEFAULT NULL, processor INT DEFAULT NULL, ram INT DEFAULT NULL, motherboard INT DEFAULT NULL, videocard INT DEFAULT NULL, hdd_first INT DEFAULT NULL, hdd_second INT DEFAULT NULL, monitor INT DEFAULT NULL, keyboard INT DEFAULT NULL, mouse INT DEFAULT NULL, name VARCHAR(255) NOT NULL, ip_address VARCHAR(60) NOT NULL, ip_type VARCHAR(60) NOT NULL, mac_address VARCHAR(60) NOT NULL, domain VARCHAR(60) DEFAULT NULL, type VARCHAR(60) NOT NULL, key_in_system VARCHAR(255) DEFAULT NULL, key_on_sticker VARCHAR(255) DEFAULT NULL, legal TINYINT(1) NOT NULL, inventory_number VARCHAR(255) DEFAULT NULL, INDEX IDX_A298A7A68C03F15C (employee_id), INDEX IDX_A298A7A6B4CDA8E9 (operation_system), INDEX IDX_A298A7A629C04650 (processor), INDEX IDX_A298A7A6E7D1222F (ram), INDEX IDX_A298A7A67F7A0F2B (motherboard), INDEX IDX_A298A7A6A1E6442D (videocard), INDEX IDX_A298A7A6836152CD (hdd_first), INDEX IDX_A298A7A6A6D47B8F (hdd_second), INDEX IDX_A298A7A6E1159985 (monitor), INDEX IDX_A298A7A683748095 (keyboard), INDEX IDX_A298A7A6AF35B6ED (mouse), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE computer_part (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, serial_number VARCHAR(255) DEFAULT NULL, inventory_number VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE computer ADD CONSTRAINT FK_A298A7A68C03F15C FOREIGN KEY (employee_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE computer ADD CONSTRAINT FK_A298A7A6B4CDA8E9 FOREIGN KEY (operation_system) REFERENCES computer_part (id)');
        $this->addSql('ALTER TABLE computer ADD CONSTRAINT FK_A298A7A629C04650 FOREIGN KEY (processor) REFERENCES computer_part (id)');
        $this->addSql('ALTER TABLE computer ADD CONSTRAINT FK_A298A7A6E7D1222F FOREIGN KEY (ram) REFERENCES computer_part (id)');
        $this->addSql('ALTER TABLE computer ADD CONSTRAINT FK_A298A7A67F7A0F2B FOREIGN KEY (motherboard) REFERENCES computer_part (id)');
        $this->addSql('ALTER TABLE computer ADD CONSTRAINT FK_A298A7A6A1E6442D FOREIGN KEY (videocard) REFERENCES computer_part (id)');
        $this->addSql('ALTER TABLE computer ADD CONSTRAINT FK_A298A7A6836152CD FOREIGN KEY (hdd_first) REFERENCES computer_part (id)');
        $this->addSql('ALTER TABLE computer ADD CONSTRAINT FK_A298A7A6A6D47B8F FOREIGN KEY (hdd_second) REFERENCES computer_part (id)');
        $this->addSql('ALTER TABLE computer ADD CONSTRAINT FK_A298A7A6E1159985 FOREIGN KEY (monitor) REFERENCES computer_part (id)');
        $this->addSql('ALTER TABLE computer ADD CONSTRAINT FK_A298A7A683748095 FOREIGN KEY (keyboard) REFERENCES computer_part (id)');
        $this->addSql('ALTER TABLE computer ADD CONSTRAINT FK_A298A7A6AF35B6ED FOREIGN KEY (mouse) REFERENCES computer_part (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE computer DROP FOREIGN KEY FK_A298A7A6B4CDA8E9');
        $this->addSql('ALTER TABLE computer DROP FOREIGN KEY FK_A298A7A629C04650');
        $this->addSql('ALTER TABLE computer DROP FOREIGN KEY FK_A298A7A6E7D1222F');
        $this->addSql('ALTER TABLE computer DROP FOREIGN KEY FK_A298A7A67F7A0F2B');
        $this->addSql('ALTER TABLE computer DROP FOREIGN KEY FK_A298A7A6A1E6442D');
        $this->addSql('ALTER TABLE computer DROP FOREIGN KEY FK_A298A7A6836152CD');
        $this->addSql('ALTER TABLE computer DROP FOREIGN KEY FK_A298A7A6A6D47B8F');
        $this->addSql('ALTER TABLE computer DROP FOREIGN KEY FK_A298A7A6E1159985');
        $this->addSql('ALTER TABLE computer DROP FOREIGN KEY FK_A298A7A683748095');
        $this->addSql('ALTER TABLE computer DROP FOREIGN KEY FK_A298A7A6AF35B6ED');
        $this->addSql('DROP TABLE computer');
        $this->addSql('DROP TABLE computer_part');
    }
}
