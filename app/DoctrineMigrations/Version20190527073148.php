<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190527073148 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE applicant_diff (id INT AUTO_INCREMENT NOT NULL, changed_by_id INT DEFAULT NULL, apllicant_id INT DEFAULT NULL, field VARCHAR(255) NOT NULL, old_value LONGTEXT DEFAULT NULL, new_value LONGTEXT DEFAULT NULL, updated_at DATETIME NOT NULL, INDEX IDX_C509642B828AD0A0 (changed_by_id), INDEX IDX_C509642BAA81F167 (apllicant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE applicant_file (id INT AUTO_INCREMENT NOT NULL, applicant_id INT DEFAULT NULL, owner_id INT DEFAULT NULL, file_size VARCHAR(255) NOT NULL, format VARCHAR(255) NOT NULL, uploaded_at DATETIME NOT NULL, deleted TINYINT(1) DEFAULT NULL, file_name LONGTEXT NOT NULL, stored_file_name LONGTEXT NOT NULL, stored_preview_file_name LONGTEXT DEFAULT NULL, stored_file_dir LONGTEXT DEFAULT NULL, full_access TINYINT(1) NOT NULL, INDEX IDX_CE6159097139001 (applicant_id), INDEX IDX_CE615907E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE applicant_file_download_manager (id INT AUTO_INCREMENT NOT NULL, applicant_file_id INT DEFAULT NULL, user_id INT DEFAULT NULL, download_date DATETIME NOT NULL, INDEX IDX_CA8E1052E58EDC95 (applicant_file_id), INDEX IDX_CA8E1052A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE applicant_comment (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, applicant_id INT DEFAULT NULL, parent_comment_id INT DEFAULT NULL, created_at DATETIME NOT NULL, comment_text LONGTEXT NOT NULL, INDEX IDX_E231F7957E3C61F9 (owner_id), INDEX IDX_E231F79597139001 (applicant_id), INDEX IDX_E231F795BF2AF943 (parent_comment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE applicant_diff ADD CONSTRAINT FK_C509642B828AD0A0 FOREIGN KEY (changed_by_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE applicant_diff ADD CONSTRAINT FK_C509642BAA81F167 FOREIGN KEY (apllicant_id) REFERENCES applicant (id)');
        $this->addSql('ALTER TABLE applicant_file ADD CONSTRAINT FK_CE6159097139001 FOREIGN KEY (applicant_id) REFERENCES applicant (id)');
        $this->addSql('ALTER TABLE applicant_file ADD CONSTRAINT FK_CE615907E3C61F9 FOREIGN KEY (owner_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE applicant_file_download_manager ADD CONSTRAINT FK_CA8E1052E58EDC95 FOREIGN KEY (applicant_file_id) REFERENCES applicant_file (id)');
        $this->addSql('ALTER TABLE applicant_file_download_manager ADD CONSTRAINT FK_CA8E1052A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE applicant_comment ADD CONSTRAINT FK_E231F7957E3C61F9 FOREIGN KEY (owner_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE applicant_comment ADD CONSTRAINT FK_E231F79597139001 FOREIGN KEY (applicant_id) REFERENCES applicant (id)');
        $this->addSql('ALTER TABLE applicant_comment ADD CONSTRAINT FK_E231F795BF2AF943 FOREIGN KEY (parent_comment_id) REFERENCES applicant_comment (id)');
        $this->addSql('ALTER TABLE applicant ADD notice LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE applicant RENAME INDEX idx_caad10197b00651c TO IDX_CAAD10196BF700BD');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE applicant_file_download_manager DROP FOREIGN KEY FK_CA8E1052E58EDC95');
        $this->addSql('ALTER TABLE applicant_comment DROP FOREIGN KEY FK_E231F795BF2AF943');
        $this->addSql('DROP TABLE applicant_diff');
        $this->addSql('DROP TABLE applicant_file');
        $this->addSql('DROP TABLE applicant_file_download_manager');
        $this->addSql('DROP TABLE applicant_comment');
        $this->addSql('ALTER TABLE applicant DROP notice');
        $this->addSql('ALTER TABLE applicant RENAME INDEX idx_caad10196bf700bd TO IDX_CAAD10197B00651C');
    }
}
