<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190421115019 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE invoice_registry (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, created_at DATETIME NOT NULL, money_limit DOUBLE PRECISION DEFAULT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_D28047527E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE invoice_registry ADD CONSTRAINT FK_D28047527E3C61F9 FOREIGN KEY (owner_id) REFERENCES fos_user_user (id)');
        $this->addSql('ALTER TABLE invoice ADD invoice_registry_id INT DEFAULT NULL, ADD owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_906517445BF965DE FOREIGN KEY (invoice_registry_id) REFERENCES invoice_registry (id)');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_906517447E3C61F9 FOREIGN KEY (owner_id) REFERENCES fos_user_user (id)');
        $this->addSql('CREATE INDEX IDX_906517445BF965DE ON invoice (invoice_registry_id)');
        $this->addSql('CREATE INDEX IDX_906517447E3C61F9 ON invoice (owner_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_906517445BF965DE');
        $this->addSql('DROP TABLE invoice_registry');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_906517447E3C61F9');
        $this->addSql('DROP INDEX IDX_906517445BF965DE ON invoice');
        $this->addSql('DROP INDEX IDX_906517447E3C61F9 ON invoice');
        $this->addSql('ALTER TABLE invoice DROP invoice_registry_id, DROP owner_id');
    }
}
