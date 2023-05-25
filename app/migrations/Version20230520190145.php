<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230520190145 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE interview_category ADD created_by INT DEFAULT NULL, ADD updated_by INT DEFAULT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE interview_category ADD CONSTRAINT FK_6F84DA22DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE interview_category ADD CONSTRAINT FK_6F84DA2216FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_6F84DA22DE12AB56 ON interview_category (created_by)');
        $this->addSql('CREATE INDEX IDX_6F84DA2216FE72E1 ON interview_category (updated_by)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE interview_category DROP FOREIGN KEY FK_6F84DA22DE12AB56');
        $this->addSql('ALTER TABLE interview_category DROP FOREIGN KEY FK_6F84DA2216FE72E1');
        $this->addSql('DROP INDEX IDX_6F84DA22DE12AB56 ON interview_category');
        $this->addSql('DROP INDEX IDX_6F84DA2216FE72E1 ON interview_category');
        $this->addSql('ALTER TABLE interview_category DROP created_by, DROP updated_by, DROP created_at, DROP updated_at');
    }
}
