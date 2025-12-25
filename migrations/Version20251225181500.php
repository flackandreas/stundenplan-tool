<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251225181500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create schedule_entry table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE schedule_entry (id INT AUTO_INCREMENT NOT NULL, lesson_id INT NOT NULL, time_slot_id INT NOT NULL, room_id INT DEFAULT NULL, school_id INT NOT NULL, INDEX IDX_5D2C007CDCDF80196 (lesson_id), INDEX IDX_5D2C007CD62F31B (time_slot_id), INDEX IDX_5D2C00754177093 (room_id), INDEX IDX_5D2C007C32A47EE (school_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE schedule_entry ADD CONSTRAINT FK_5D2C007CDCDF80196 FOREIGN KEY (lesson_id) REFERENCES lesson (id)');
        $this->addSql('ALTER TABLE schedule_entry ADD CONSTRAINT FK_5D2C007CD62F31B FOREIGN KEY (time_slot_id) REFERENCES time_slot (id)');
        $this->addSql('ALTER TABLE schedule_entry ADD CONSTRAINT FK_5D2C00754177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('ALTER TABLE schedule_entry ADD CONSTRAINT FK_5D2C007C32A47EE FOREIGN KEY (school_id) REFERENCES school (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE schedule_entry');
    }
}
