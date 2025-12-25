<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251225100133 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE lesson (id INT AUTO_INCREMENT NOT NULL, hours_per_week INT NOT NULL, is_double_period TINYINT NOT NULL, subject_id INT NOT NULL, school_id INT NOT NULL, teacher_id INT NOT NULL, student_class_id INT NOT NULL, room_id INT DEFAULT NULL, INDEX IDX_F87474F323EDC87 (subject_id), INDEX IDX_F87474F3C32A47EE (school_id), INDEX IDX_F87474F341807E1D (teacher_id), INDEX IDX_F87474F3598B478B (student_class_id), INDEX IDX_F87474F354177093 (room_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE room (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, capacity INT DEFAULT NULL, school_id INT NOT NULL, INDEX IDX_729F519BC32A47EE (school_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE school (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, settings JSON DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE student_class (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, level INT DEFAULT NULL, school_id INT NOT NULL, INDEX IDX_657C6002C32A47EE (school_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE subject (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, shortcode VARCHAR(10) NOT NULL, color VARCHAR(7) DEFAULT NULL, school_id INT NOT NULL, INDEX IDX_FBCE3E7AC32A47EE (school_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE teacher (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(2552) NOT NULL, shortcode VARCHAR(10) NOT NULL, email VARCHAR(255) DEFAULT NULL, school_id INT NOT NULL, INDEX IDX_B0F6A6D5C32A47EE (school_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE teacher_availability (id INT AUTO_INCREMENT NOT NULL, day_of_week INT NOT NULL, start_time TIME DEFAULT NULL, end_time TIME DEFAULT NULL, type VARCHAR(20) NOT NULL, reason VARCHAR(255) DEFAULT NULL, school_id INT NOT NULL, teacher_id INT NOT NULL, INDEX IDX_A968568DC32A47EE (school_id), INDEX IDX_A968568D41807E1D (teacher_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE time_slot (id INT AUTO_INCREMENT NOT NULL, day_of_week INT NOT NULL, period_number INT NOT NULL, start_time TIME NOT NULL, end_time TIME NOT NULL, school_id INT NOT NULL, INDEX IDX_1B3294AC32A47EE (school_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, school_id INT NOT NULL, teacher_id INT DEFAULT NULL, INDEX IDX_8D93D649C32A47EE (school_id), UNIQUE INDEX UNIQ_8D93D64941807E1D (teacher_id), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F323EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id)');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F3C32A47EE FOREIGN KEY (school_id) REFERENCES school (id)');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F341807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id)');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F3598B478B FOREIGN KEY (student_class_id) REFERENCES student_class (id)');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F354177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519BC32A47EE FOREIGN KEY (school_id) REFERENCES school (id)');
        $this->addSql('ALTER TABLE student_class ADD CONSTRAINT FK_657C6002C32A47EE FOREIGN KEY (school_id) REFERENCES school (id)');
        $this->addSql('ALTER TABLE subject ADD CONSTRAINT FK_FBCE3E7AC32A47EE FOREIGN KEY (school_id) REFERENCES school (id)');
        $this->addSql('ALTER TABLE teacher ADD CONSTRAINT FK_B0F6A6D5C32A47EE FOREIGN KEY (school_id) REFERENCES school (id)');
        $this->addSql('ALTER TABLE teacher_availability ADD CONSTRAINT FK_A968568DC32A47EE FOREIGN KEY (school_id) REFERENCES school (id)');
        $this->addSql('ALTER TABLE teacher_availability ADD CONSTRAINT FK_A968568D41807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id)');
        $this->addSql('ALTER TABLE time_slot ADD CONSTRAINT FK_1B3294AC32A47EE FOREIGN KEY (school_id) REFERENCES school (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649C32A47EE FOREIGN KEY (school_id) REFERENCES school (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64941807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F323EDC87');
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F3C32A47EE');
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F341807E1D');
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F3598B478B');
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F354177093');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519BC32A47EE');
        $this->addSql('ALTER TABLE student_class DROP FOREIGN KEY FK_657C6002C32A47EE');
        $this->addSql('ALTER TABLE subject DROP FOREIGN KEY FK_FBCE3E7AC32A47EE');
        $this->addSql('ALTER TABLE teacher DROP FOREIGN KEY FK_B0F6A6D5C32A47EE');
        $this->addSql('ALTER TABLE teacher_availability DROP FOREIGN KEY FK_A968568DC32A47EE');
        $this->addSql('ALTER TABLE teacher_availability DROP FOREIGN KEY FK_A968568D41807E1D');
        $this->addSql('ALTER TABLE time_slot DROP FOREIGN KEY FK_1B3294AC32A47EE');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649C32A47EE');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64941807E1D');
        $this->addSql('DROP TABLE lesson');
        $this->addSql('DROP TABLE room');
        $this->addSql('DROP TABLE school');
        $this->addSql('DROP TABLE student_class');
        $this->addSql('DROP TABLE subject');
        $this->addSql('DROP TABLE teacher');
        $this->addSql('DROP TABLE teacher_availability');
        $this->addSql('DROP TABLE time_slot');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
