<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261016215158 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE booking (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(10) NOT NULL, booked_at DATETIME NOT NULL, cancelled_at DATETIME DEFAULT NULL, passenger_id INT NOT NULL, ride_id INT NOT NULL, UNIQUE INDEX UNIQ_E00CEDDE4502E565302A8A70 (passenger_id, ride_id), INDEX IDX_E00CEDDE4502E565 (passenger_id), INDEX IDX_E00CEDDE302A8A70 (ride_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, content LONGTEXT NOT NULL, sent_at DATETIME NOT NULL, is_read TINYINT DEFAULT 0 NOT NULL, sender_id INT NOT NULL, ride_id INT NOT NULL, INDEX IDX_B6BD307F302A8A7096E4F388 (ride_id, sent_at), INDEX IDX_B6BD307FF624B39D (sender_id), INDEX IDX_B6BD307F302A8A70 (ride_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(50) NOT NULL, title VARCHAR(150) NOT NULL, message LONGTEXT NOT NULL, is_read TINYINT DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, user_id INT NOT NULL, INDEX IDX_BF5476CAA76ED395DA46F46 (user_id, is_read), INDEX IDX_BF5476CAA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, punctuality SMALLINT UNSIGNED NOT NULL, driving SMALLINT UNSIGNED NOT NULL, courtesy SMALLINT UNSIGNED NOT NULL, comment LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, author_id INT NOT NULL, ride_id INT NOT NULL, UNIQUE INDEX UNIQ_794381C6F675F31B302A8A70 (author_id, ride_id), INDEX IDX_794381C6F675F31B (author_id), INDEX IDX_794381C6302A8A70 (ride_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE ride (id INT AUTO_INCREMENT NOT NULL, departure VARCHAR(150) NOT NULL, destination VARCHAR(150) NOT NULL, departure_at DATETIME NOT NULL, gender VARCHAR(10) NOT NULL, available_seats SMALLINT UNSIGNED NOT NULL, price NUMERIC(8, 2) NOT NULL, suggested_price NUMERIC(8, 2) DEFAULT NULL, status VARCHAR(10) NOT NULL, smoking_allowed TINYINT DEFAULT 0 NOT NULL, luggage_allowed TINYINT DEFAULT 1 NOT NULL, music_allowed TINYINT DEFAULT 1 NOT NULL, instant_booking TINYINT DEFAULT 0 NOT NULL, driver_id INT NOT NULL, INDEX IDX_9B3D7CD07B00651CA26A776A (status, departure_at), INDEX IDX_9B3D7CD0C3423909 (driver_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE setting (id INT AUTO_INCREMENT NOT NULL, cost_per_km NUMERIC(6, 3) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, email VARCHAR(180) NOT NULL, gender VARCHAR(10) NOT NULL, password VARCHAR(255) NOT NULL, occupation VARCHAR(10) NOT NULL, status VARCHAR(10) DEFAULT \'active\' NOT NULL, student_card VARCHAR(50) DEFAULT NULL, field_of_study VARCHAR(100) DEFAULT NULL, role VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE vehicle (id INT AUTO_INCREMENT NOT NULL, brand VARCHAR(50) NOT NULL, model VARCHAR(50) NOT NULL, color VARCHAR(30) NOT NULL, seats SMALLINT UNSIGNED NOT NULL, owner_id INT NOT NULL, UNIQUE INDEX UNIQ_1B80E4867E3C61F9 (owner_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE4502E565 FOREIGN KEY (passenger_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE302A8A70 FOREIGN KEY (ride_id) REFERENCES ride (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F302A8A70 FOREIGN KEY (ride_id) REFERENCES ride (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6F675F31B FOREIGN KEY (author_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6302A8A70 FOREIGN KEY (ride_id) REFERENCES ride (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ride ADD CONSTRAINT FK_9B3D7CD0C3423909 FOREIGN KEY (driver_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE vehicle ADD CONSTRAINT FK_1B80E4867E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE4502E565');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE302A8A70');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF624B39D');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F302A8A70');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA76ED395');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6F675F31B');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6302A8A70');
        $this->addSql('ALTER TABLE ride DROP FOREIGN KEY FK_9B3D7CD0C3423909');
        $this->addSql('ALTER TABLE vehicle DROP FOREIGN KEY FK_1B80E4867E3C61F9');
        $this->addSql('DROP TABLE booking');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE ride');
        $this->addSql('DROP TABLE setting');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE vehicle');
    }
}
