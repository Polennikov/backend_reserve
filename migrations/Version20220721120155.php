<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220721120155 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE employer_work_cache_info DROP CONSTRAINT fk_3133c1f3d92a6c');
        $this->addSql('DROP SEQUENCE employer_work_cache_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE employer_work_cache_info_id_seq CASCADE');
        $this->addSql('DROP TABLE employer_work_cache');
        $this->addSql('DROP TABLE employer_work_cache_info');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE employer_work_cache_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE employer_work_cache_info_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE employer_work_cache (id INT NOT NULL, employer_id INT NOT NULL, month INT NOT NULL, year INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE employer_work_cache_info (id INT NOT NULL, employer_work_id INT NOT NULL, project_id INT NOT NULL, percent DOUBLE PRECISION NOT NULL, hours DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_3133c1f3d92a6c ON employer_work_cache_info (employer_work_id)');
        $this->addSql('ALTER TABLE employer_work_cache_info ADD CONSTRAINT fk_3133c1f3d92a6c FOREIGN KEY (employer_work_id) REFERENCES employer_work_cache (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
