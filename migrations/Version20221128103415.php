<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221128103415 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE report_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE task_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE report (id INT NOT NULL, manager_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, statuses VARCHAR(600) DEFAULT NULL, from_date DATE DEFAULT NULL, to_date DATE DEFAULT NULL, filter_settings JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C42F7784783E3463 ON report (manager_id)');
        $this->addSql('CREATE TABLE task (id INT NOT NULL, report_id INT NOT NULL, title VARCHAR(255) NOT NULL, redmine_estimate DOUBLE PRECISION DEFAULT NULL, redmine_total_spend DOUBLE PRECISION DEFAULT NULL, evo_total_spend DOUBLE PRECISION DEFAULT NULL, redmine_id INT DEFAULT NULL, status VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_527EDB254BD2A4C0 ON task (report_id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784783E3463 FOREIGN KEY (manager_id) REFERENCES manager (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB254BD2A4C0 FOREIGN KEY (report_id) REFERENCES report (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB254BD2A4C0');
        $this->addSql('DROP SEQUENCE report_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE task_id_seq CASCADE');
        $this->addSql('DROP TABLE report');
        $this->addSql('DROP TABLE task');
    }
}
