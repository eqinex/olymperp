<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190627104928 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE book_file_download_manager (id INT AUTO_INCREMENT NOT NULL, book_file_id INT DEFAULT NULL, user_id INT DEFAULT NULL, download_date DATETIME NOT NULL, INDEX IDX_11127A19EB51FA (book_file_id), INDEX IDX_11127A19A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_diff (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, book_id INT DEFAULT NULL, status LONGTEXT DEFAULT NULL, updated_at DATETIME NOT NULL, INDEX IDX_5CED0CA3A76ED395 (user_id), INDEX IDX_5CED0CA316A2B381 (book_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, author VARCHAR(255) DEFAULT NULL, editor VARCHAR(255) DEFAULT NULL, year_of_issue VARCHAR(255) NOT NULL, publishing_house VARCHAR(255) DEFAULT NULL, genre VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_file (id INT AUTO_INCREMENT NOT NULL, book_id INT DEFAULT NULL, owner_id INT DEFAULT NULL, file_size VARCHAR(255) NOT NULL, format VARCHAR(255) NOT NULL, uploaded_at DATETIME NOT NULL, deleted TINYINT(1) DEFAULT NULL, file_name LONGTEXT NOT NULL, stored_file_name LONGTEXT NOT NULL, stored_preview_file_name LONGTEXT DEFAULT NULL, stored_file_dir LONGTEXT DEFAULT NULL, full_access TINYINT(1) NOT NULL, INDEX IDX_95027D1816A2B381 (book_id), INDEX IDX_95027D187E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_book (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, book_id INT DEFAULT NULL, INDEX IDX_B164EFF8A76ED395 (user_id), INDEX IDX_B164EFF816A2B381 (book_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE book_file_download_manager ADD CONSTRAINT FK_11127A19EB51FA FOREIGN KEY (book_file_id) REFERENCES book_file (id)');
        $this->addSql('ALTER TABLE book_file_download_manager ADD CONSTRAINT FK_11127A19A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE book_diff ADD CONSTRAINT FK_5CED0CA3A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE book_diff ADD CONSTRAINT FK_5CED0CA316A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE book_file ADD CONSTRAINT FK_95027D1816A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE book_file ADD CONSTRAINT FK_95027D187E3C61F9 FOREIGN KEY (owner_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE user_book ADD CONSTRAINT FK_B164EFF8A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE user_book ADD CONSTRAINT FK_B164EFF816A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE book_diff DROP FOREIGN KEY FK_5CED0CA316A2B381');
        $this->addSql('ALTER TABLE book_file DROP FOREIGN KEY FK_95027D1816A2B381');
        $this->addSql('ALTER TABLE user_book DROP FOREIGN KEY FK_B164EFF816A2B381');
        $this->addSql('ALTER TABLE book_file_download_manager DROP FOREIGN KEY FK_11127A19EB51FA');
        $this->addSql('DROP TABLE book_file_download_manager');
        $this->addSql('DROP TABLE book_diff');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE book_file');
        $this->addSql('DROP TABLE user_book');
    }
}
