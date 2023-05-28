<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221128125013 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE spending_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE spending (id INT NOT NULL, task_id INT NOT NULL, month INT NOT NULL, time_evo DOUBLE PRECISION DEFAULT NULL, time_redmine DOUBLE PRECISION DEFAULT NULL, year INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E44ECDD8DB60186 ON spending (task_id)');
        $this->addSql('ALTER TABLE spending ADD CONSTRAINT FK_E44ECDD8DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE report DROP statuses');
        $this->addSql('ALTER TABLE task ADD from_date DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE task ADD to_date DATE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE spending_id_seq CASCADE');
        $this->addSql('DROP TABLE spending');
        $this->addSql('ALTER TABLE report ADD statuses VARCHAR(600) DEFAULT NULL');
        $this->addSql('ALTER TABLE task DROP from_date');
        $this->addSql('ALTER TABLE task DROP to_date');
    }
}
