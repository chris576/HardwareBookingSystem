<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231211150230 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role_hardware (role_id INT NOT NULL, hardware_id INT NOT NULL, INDEX IDX_D44A0FD1D60322AC (role_id), INDEX IDX_D44A0FD1C9CC762B (hardware_id), PRIMARY KEY(role_id, hardware_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role_user (role_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_332CA4DDD60322AC (role_id), INDEX IDX_332CA4DDA76ED395 (user_id), PRIMARY KEY(role_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE role_hardware ADD CONSTRAINT FK_D44A0FD1D60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE role_hardware ADD CONSTRAINT FK_D44A0FD1C9CC762B FOREIGN KEY (hardware_id) REFERENCES hardware (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE role_user ADD CONSTRAINT FK_332CA4DDD60322AC FOREIGN KEY (role_id) REFERENCES role (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE role_user ADD CONSTRAINT FK_332CA4DDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('INSERT INTO role (id, name) VALUES (:id, :name)', [
            'id' => 1,
            'name' => 'ROLE_ADMIN'
        ]);
        $this->addSql('INSERT INTO role (id, name) VALUES (:id, :name)', [
            'id' => 2,
            'name' => 'ROLE_USER'
        ]);
        $this->addSql('INSERT INTO role (id, name) VALUES (:id, :name)', [
            'id' => 3,
            'name' => 'ROLE_INTERN'
        ]);
        $this->addSql('INSERT INTO role (id, name) VALUES (:id, :name)', [
            'id' => 4,
            'name' => 'ROLE_EXTERN'
        ]);
        $this->addSql('INSERT INTO user (id, email, password) VALUES (:id, :email, :password)', [
            'id' => 1,
            'email' => 'root@root.com',
            'password' => password_hash('123', PASSWORD_BCRYPT),
        ]);
        $this->addSql('INSERT INTO role_user (role_id, user_id) VALUES (:role_id, :user_id)', [
            'role_id' => 1,
            'user_id' => 1,
        ]);
        $this->addSql('INSERT INTO role_user (role_id, user_id) VALUES (:role_id, :user_id)', [
            'role_id' => 2,
            'user_id' => 1,
        ]);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE role_hardware DROP FOREIGN KEY FK_D44A0FD1D60322AC');
        $this->addSql('ALTER TABLE role_hardware DROP FOREIGN KEY FK_D44A0FD1C9CC762B');
        $this->addSql('ALTER TABLE role_user DROP FOREIGN KEY FK_332CA4DDD60322AC');
        $this->addSql('ALTER TABLE role_user DROP FOREIGN KEY FK_332CA4DDA76ED395');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE role_hardware');
        $this->addSql('DROP TABLE role_user');
    }
}
