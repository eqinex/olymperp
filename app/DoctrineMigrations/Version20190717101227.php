<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190717101227 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $genres = $this->connection->executeQuery("SELECT * FROM book_genre")->fetchAll();

        $this->addSql('CREATE TABLE genre (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        foreach ($genres as $genre) {
            $this->addSql("INSERT INTO genre SET name='" . $genre['name'] . "'");
        }
        $this->addSql('ALTER TABLE book_genre ADD book_id INT DEFAULT NULL, ADD genre_id INT DEFAULT NULL, DROP name');
        $this->addSql('ALTER TABLE book_genre ADD CONSTRAINT FK_8D92268116A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE book_genre ADD CONSTRAINT FK_8D9226814296D31F FOREIGN KEY (genre_id) REFERENCES genre (id)');
        $this->addSql('CREATE INDEX IDX_8D92268116A2B381 ON book_genre (book_id)');
        $this->addSql('CREATE INDEX IDX_8D9226814296D31F ON book_genre (genre_id)');
        $this->addSql('ALTER TABLE book DROP genre');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $genres = $this->connection->executeQuery("SELECT * FROM genre")->fetchAll();

        $this->addSql('ALTER TABLE book_genre DROP FOREIGN KEY FK_8D9226814296D31F');
        $this->addSql('DROP TABLE genre');
        $this->addSql('ALTER TABLE book ADD genre VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE book_genre DROP FOREIGN KEY FK_8D92268116A2B381');
        $this->addSql('DROP INDEX IDX_8D92268116A2B381 ON book_genre');
        $this->addSql('DROP INDEX IDX_8D9226814296D31F ON book_genre');
        $this->addSql('ALTER TABLE book_genre ADD name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, DROP book_id, DROP genre_id');
        foreach ($genres as $genre) {
            $this->addSql("INSERT INTO book_genre SET name='" . $genre['name'] . "'");
        }
    }
}
