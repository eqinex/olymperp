<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190613063857 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE production_calendar (id INT AUTO_INCREMENT NOT NULL, dateStart DATETIME NOT NULL, dateEnd DATETIME NOT NULL, type VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql(
            'INSERT INTO `production_calendar` (`dateStart`,`dateEnd`,`type`,`description`) VALUES (\'2018-12-30 00:00:00\',\'2019-01-08 00:00:00\',\'holiday\',\'Новогодние каникулы 2019\');
            INSERT INTO `production_calendar` (`dateStart`,`dateEnd`,`type`,`description`) VALUES (\'2019-03-08 00:00:00\',\'2019-03-08 00:00:00\',\'holiday\',\'Международный женский день\');
            INSERT INTO `production_calendar` (`dateStart`,`dateEnd`,`type`,`description`) VALUES (\'2019-06-12 00:00:00\',\'2019-06-12 00:00:00\',\'holiday\',\'День России\');
            INSERT INTO `production_calendar` (`dateStart`,`dateEnd`,`type`,`description`) VALUES (\'2019-05-01 00:00:00\',\'2019-05-05 00:00:00\',\'holiday\',\'Праздник весны и труда\');
            INSERT INTO `production_calendar` (`dateStart`,`dateEnd`,`type`,`description`) VALUES (\'2019-05-09 00:00:00\',\'2019-05-12 00:00:00\',\'holiday\',\'День Победы (вторые майские)\');
            INSERT INTO `production_calendar` (`dateStart`,`dateEnd`,`type`,`description`) VALUES (\'2019-11-04 00:00:00\',\'2019-06-04 00:00:00\',\'holiday\',\'День народного единства\');
            INSERT INTO `production_calendar` (`dateStart`,`dateEnd`,`type`,`description`) VALUES (\'2019-02-23 00:00:00\',\'2019-02-23 00:00:00\',\'holiday\',\'День защитника Отечества (выходной перенесен на 2019-05-10)\');
            INSERT INTO `production_calendar` (`dateStart`,`dateEnd`,`type`,`description`) VALUES (\'2019-03-07 00:00:00\',\'2019-03-07 00:00:00\',\'shortened_day\',\'Сокращенный рабочий день\');
            INSERT INTO `production_calendar` (`dateStart`,`dateEnd`,`type`,`description`) VALUES (\'2019-06-11 00:00:00\',\'2019-06-11 00:00:00\',\'shortened_day\',\'Сокращенный рабочий день\');
            INSERT INTO `production_calendar` (`dateStart`,`dateEnd`,`type`,`description`) VALUES (\'2019-05-08 00:00:00\',\'2019-05-08 00:00:00\',\'shortened_day\',\'Сокращенный рабочий день\');
            INSERT INTO `production_calendar` (`dateStart`,`dateEnd`,`type`,`description`) VALUES (\'2019-12-31 00:00:00\',\'2019-12-31 00:00:00\',\'shortened_day\',\'Сокращенный рабочий день\');
            INSERT INTO `production_calendar` (`dateStart`,`dateEnd`,`type`,`description`) VALUES (\'2019-04-30 00:00:00\',\'2019-04-30 00:00:00\',\'shortened_day\',\'Сокращенный рабочий день\');
            INSERT INTO `production_calendar` (`dateStart`,`dateEnd`,`type`,`description`) VALUES (\'2020-01-01 00:00:00\',\'2020-01-08 00:00:00\',\'holiday\',\'Новогодние каникулы 2020\');
            INSERT INTO `production_calendar` (`dateStart`,`dateEnd`,`type`,`description`) VALUES (\'2020-02-23 00:00:00\',\'2020-02-24 00:00:00\',\'holiday\',\'День защитника Отечества (выходной перенесен на 2020-02-24)\');
            INSERT INTO `production_calendar` (`dateStart`,`dateEnd`,`type`,`description`) VALUES (\'2020-03-08 00:00:00\',\'2020-03-09 00:00:00\',\'holiday\',\'Международный женский день (выходной перенесен на 2020-03-09)\');
            INSERT INTO `production_calendar` (`dateStart`,`dateEnd`,`type`,`description`) VALUES (\'2020-04-30 00:00:00\',\'2020-04-30 00:00:00\',\'shortened_day\',\'Сокращенный рабочий день\');
            INSERT INTO `production_calendar` (`dateStart`,`dateEnd`,`type`,`description`) VALUES (\'2020-05-01 00:00:00\',\'2020-05-04 00:00:00\',\'holiday\',\'Праздник весны и труда (первые майские)\');
            INSERT INTO `production_calendar` (`dateStart`,`dateEnd`,`type`,`description`) VALUES (\'2020-05-09 00:00:00\',\'2020-05-12 00:00:00\',\'holiday\',\'День Победы (вторые майские)\');
            INSERT INTO `production_calendar` (`dateStart`,`dateEnd`,`type`,`description`) VALUES (\'2020-05-08 00:00:00\',\'2020-05-08 00:00:00\',\'shortened_day\',\'Сокращенный рабочий день\');
            INSERT INTO `production_calendar` (`dateStart`,`dateEnd`,`type`,`description`) VALUES (\'2020-06-11 00:00:00\',\'2020-06-11 00:00:00\',\'shortened_day\',\'Сокращенный рабочий день\');
            INSERT INTO `production_calendar` (`dateStart`,`dateEnd`,`type`,`description`) VALUES (\'2020-06-12 00:00:00\',\'2020-06-12 00:00:00\',\'holiday\',\'День России\');
            INSERT INTO `production_calendar` (`dateStart`,`dateEnd`,`type`,`description`) VALUES (\'2020-11-03 00:00:00\',\'2020-11-03 00:00:00\',\'shortened_day\',\'Сокращенный рабочий день\');
            INSERT INTO `production_calendar` (`dateStart`,`dateEnd`,`type`,`description`) VALUES (\'2020-11-04 00:00:00\',\'2020-11-04 00:00:00\',\'holiday\',\'День народного единства\');
            INSERT INTO `production_calendar` (`dateStart`,`dateEnd`,`type`,`description`) VALUES (\'2020-12-31 00:00:00\',\'2020-12-31 00:00:00\',\'shortened_day\',\'Сокращенный рабочий день\');'
        );
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE production_calendar');

    }
}
