<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220217151012 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE employer_work_cache_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE employer_work_cache_info_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE manager_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE employer_work_cache (id INT NOT NULL, employer_id INT NOT NULL, month INT NOT NULL, year INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE employer_work_cache_info (id INT NOT NULL, employer_work_id INT NOT NULL, project_id INT NOT NULL, percent INT NOT NULL, hours INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3133C1F3D92A6C ON employer_work_cache_info (employer_work_id)');
        $this->addSql('CREATE TABLE manager (id INT NOT NULL, token VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE employer_work_cache_info ADD CONSTRAINT FK_3133C1F3D92A6C FOREIGN KEY (employer_work_id) REFERENCES employer_work_cache (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE employer_work_cache_info DROP CONSTRAINT FK_3133C1F3D92A6C');
        $this->addSql('DROP SEQUENCE employer_work_cache_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE employer_work_cache_info_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE manager_id_seq CASCADE');
        $this->addSql('DROP TABLE employer_work_cache');
        $this->addSql('DROP TABLE employer_work_cache_info');
        $this->addSql('DROP TABLE manager');
    }
}
