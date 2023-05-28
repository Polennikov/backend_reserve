<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220421091106 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE employer_work_cache_info ALTER percent TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE employer_work_cache_info ALTER percent DROP DEFAULT');
        $this->addSql('ALTER TABLE employer_work_cache_info ALTER hours TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE employer_work_cache_info ALTER hours DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE employer_work_cache_info ALTER percent TYPE INT');
        $this->addSql('ALTER TABLE employer_work_cache_info ALTER percent DROP DEFAULT');
        $this->addSql('ALTER TABLE employer_work_cache_info ALTER hours TYPE INT');
        $this->addSql('ALTER TABLE employer_work_cache_info ALTER hours DROP DEFAULT');
    }
}
