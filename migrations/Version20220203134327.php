<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220203134327 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE reservation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE reservation_change_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE reservation (id INT NOT NULL, manager_id INT NOT NULL, project_id INT NOT NULL, employer_id INT NOT NULL, month INT NOT NULL, year INT NOT NULL, percent INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE reservation_change (id INT NOT NULL, entry_id INT NOT NULL, change_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, old_value INT NOT NULL, new_value INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8A6B7BBBA364942 ON reservation_change (entry_id)');
        $this->addSql('ALTER TABLE reservation_change ADD CONSTRAINT FK_8A6B7BBBA364942 FOREIGN KEY (entry_id) REFERENCES reservation (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE reservation_change DROP CONSTRAINT FK_8A6B7BBBA364942');
        $this->addSql('DROP SEQUENCE reservation_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE reservation_change_id_seq CASCADE');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE reservation_change');
    }
}
