<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221121122228 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE job_sector CHANGE name name LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE profile CHANGE year_of_experience year_of_experience LONGTEXT DEFAULT NULL, CHANGE degree degree LONGTEXT DEFAULT NULL, CHANGE full_address full_address LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE job_sector CHANGE name name TINYTEXT NOT NULL');
        $this->addSql('ALTER TABLE profile CHANGE year_of_experience year_of_experience VARCHAR(255) DEFAULT NULL, CHANGE degree degree VARCHAR(255) DEFAULT NULL, CHANGE full_address full_address VARCHAR(255) DEFAULT NULL');
    }
}
