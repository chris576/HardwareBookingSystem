<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230904083242 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE booking (id INT AUTO_INCREMENT NOT NULL, hardware_id INT NOT NULL, user_id INT NOT NULL, start_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', end_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_E00CEDDEC9CC762B (hardware_id), INDEX IDX_E00CEDDEA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hardware (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, ip_v4 VARCHAR(18) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEC9CC762B FOREIGN KEY (hardware_id) REFERENCES hardware (id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('
            CREATE PROCEDURE calculateBookables(IN bookingDate DATETIME, IN hardwareId INT, IN bookingLength INT)
            BEGIN
            WITH RECURSIVE MyHours AS (
                SELECT bookingDate AS myTimestamp
                UNION ALL
                SELECT DATE_ADD(myTimestamp, INTERVAL 1 HOUR)
                FROM MyHours
                WHERE DATE_ADD(myTimestamp, INTERVAL 1 HOUR) <= DATE_ADD(
                        bookingDate,
                        INTERVAL 24 - EXTRACT(
                            HOUR
                            FROM bookingDate
                        ) HOUR
                    )
            )
            SELECT DISTINCT h1.myTimestamp as startDateTime,
                h2.myTimestamp as endDateTime
            FROM MyHours AS h1,
                MyHours as h2
            WHERE TIMESTAMPDIFF(HOUR, h1.myTimestamp, h2.myTimestamp) = bookingLength
                AND (
                    SELECT COUNT(*)
                    FROM booking
                    WHERE hardware_id = hardwareId
                        AND (
                            DATE_ADD(h1.myTimestamp, INTERVAL 1 SECOND) BETWEEN start_date AND end_date
                            OR DATE_SUB(h2.myTimestamp, INTERVAL 1 SECOND) BETWEEN start_date AND end_date
                        )
                ) = 0;
            END;
            ');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDEC9CC762B');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDEA76ED395');
        $this->addSql('DROP TABLE booking');
        $this->addSql('DROP TABLE hardware');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP PROCEDURE calculateBookables');
    }
}