<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221019121027 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE batch_customer (id INT AUTO_INCREMENT NOT NULL, deal_id INT NOT NULL, sending_date DATE NOT NULL, batch_number INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_AD7E069F60E2305 (deal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, manager VARCHAR(255) DEFAULT NULL, interlocutor VARCHAR(255) DEFAULT NULL, locality VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer_job_sector (customer_id INT NOT NULL, job_sector_id INT NOT NULL, INDEX IDX_E66EF0EB9395C3F3 (customer_id), INDEX IDX_E66EF0EB19252776 (job_sector_id), PRIMARY KEY(customer_id, job_sector_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE deal (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, job_name VARCHAR(255) NOT NULL, job_description LONGTEXT DEFAULT NULL, deadline DATE DEFAULT NULL, comment LONGTEXT DEFAULT NULL, salary_min INT DEFAULT NULL, salary_max INT DEFAULT NULL, salary_exact INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_E3FEC1169395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE deal_user (deal_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_3BEEB3E6F60E2305 (deal_id), INDEX IDX_3BEEB3E6A76ED395 (user_id), PRIMARY KEY(deal_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profile (id INT AUTO_INCREMENT NOT NULL, received_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', full_name VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(255) DEFAULT NULL, years_old INT DEFAULT NULL, gender VARCHAR(255) DEFAULT NULL, actually_job_title VARCHAR(255) DEFAULT NULL, hope_job_title VARCHAR(255) DEFAULT NULL, year_of_experience DOUBLE PRECISION DEFAULT NULL, timeline_experience LONGTEXT DEFAULT NULL, degree VARCHAR(255) DEFAULT NULL, locality VARCHAR(255) DEFAULT NULL, full_address VARCHAR(255) DEFAULT NULL, current_salary INT DEFAULT NULL, salary_expectation_min INT DEFAULT NULL, salary_expectation_max INT DEFAULT NULL, current_state VARCHAR(255) DEFAULT NULL, comment LONGTEXT DEFAULT NULL, source VARCHAR(255) DEFAULT NULL, curriculum_vitae VARCHAR(255) DEFAULT NULL, profile_photo VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profile_job_sector (profile_id INT NOT NULL, job_sector_id INT NOT NULL, INDEX IDX_DCCCC334CCFA12B8 (profile_id), INDEX IDX_DCCCC33419252776 (job_sector_id), PRIMARY KEY(profile_id, job_sector_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE batch_customer ADD CONSTRAINT FK_AD7E069F60E2305 FOREIGN KEY (deal_id) REFERENCES deal (id)');
        $this->addSql('ALTER TABLE customer_job_sector ADD CONSTRAINT FK_E66EF0EB9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE customer_job_sector ADD CONSTRAINT FK_E66EF0EB19252776 FOREIGN KEY (job_sector_id) REFERENCES job_sector (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE deal ADD CONSTRAINT FK_E3FEC1169395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE deal_user ADD CONSTRAINT FK_3BEEB3E6F60E2305 FOREIGN KEY (deal_id) REFERENCES deal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE deal_user ADD CONSTRAINT FK_3BEEB3E6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE profile_job_sector ADD CONSTRAINT FK_DCCCC334CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE profile_job_sector ADD CONSTRAINT FK_DCCCC33419252776 FOREIGN KEY (job_sector_id) REFERENCES job_sector (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE batch_customer DROP FOREIGN KEY FK_AD7E069F60E2305');
        $this->addSql('ALTER TABLE customer_job_sector DROP FOREIGN KEY FK_E66EF0EB9395C3F3');
        $this->addSql('ALTER TABLE customer_job_sector DROP FOREIGN KEY FK_E66EF0EB19252776');
        $this->addSql('ALTER TABLE deal DROP FOREIGN KEY FK_E3FEC1169395C3F3');
        $this->addSql('ALTER TABLE deal_user DROP FOREIGN KEY FK_3BEEB3E6F60E2305');
        $this->addSql('ALTER TABLE deal_user DROP FOREIGN KEY FK_3BEEB3E6A76ED395');
        $this->addSql('ALTER TABLE profile_job_sector DROP FOREIGN KEY FK_DCCCC334CCFA12B8');
        $this->addSql('ALTER TABLE profile_job_sector DROP FOREIGN KEY FK_DCCCC33419252776');
        $this->addSql('DROP TABLE batch_customer');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE customer_job_sector');
        $this->addSql('DROP TABLE deal');
        $this->addSql('DROP TABLE deal_user');
        $this->addSql('DROP TABLE profile');
        $this->addSql('DROP TABLE profile_job_sector');
    }
}
