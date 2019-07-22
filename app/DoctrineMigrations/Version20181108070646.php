<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181108070646 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE supplier ADD full_title VARCHAR(255) DEFAULT NULL, ADD legal_address VARCHAR(255) DEFAULT NULL, ADD actual_address VARCHAR(255) DEFAULT NULL, ADD email VARCHAR(255) DEFAULT NULL, ADD site VARCHAR(255) DEFAULT NULL, ADD phone VARCHAR(255) DEFAULT NULL, ADD fax VARCHAR(255) DEFAULT NULL, ADD ogrn VARCHAR(255) DEFAULT NULL, ADD kpp VARCHAR(255) DEFAULT NULL, ADD okpo VARCHAR(255) DEFAULT NULL, ADD okved VARCHAR(255) DEFAULT NULL, ADD okfs VARCHAR(255) DEFAULT NULL, ADD okopf VARCHAR(255) DEFAULT NULL, ADD okato VARCHAR(255) DEFAULT NULL, ADD director VARCHAR(255) DEFAULT NULL, ADD basis VARCHAR(255) DEFAULT NULL, ADD accountant VARCHAR(255) DEFAULT NULL, ADD registeredAt DATETIME DEFAULT NULL, ADD checking_account VARCHAR(255) DEFAULT NULL, ADD bank_name VARCHAR(255) DEFAULT NULL, ADD correspondent_account VARCHAR(255) DEFAULT NULL, ADD bic VARCHAR(255) DEFAULT NULL, ADD bank_mailing_address VARCHAR(255) DEFAULT NULL, ADD bank_legal_address VARCHAR(255) DEFAULT NULL, ADD bank_actual_address VARCHAR(255) DEFAULT NULL, ADD bank_itn VARCHAR(255) DEFAULT NULL, ADD bank_kpp VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE supplier DROP full_title, DROP legal_address, DROP actual_address, DROP email, DROP site, DROP phone, DROP fax, DROP ogrn, DROP kpp, DROP okpo, DROP okved, DROP okfs, DROP okopf, DROP okato, DROP director, DROP basis, DROP accountant, DROP registeredAt, DROP checking_account, DROP bank_name, DROP correspondent_account, DROP bic, DROP bank_mailing_address, DROP bank_legal_address, DROP bank_actual_address, DROP bank_itn, DROP bank_kpp');
    }
}
