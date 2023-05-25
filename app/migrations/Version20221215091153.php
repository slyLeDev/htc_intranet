<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221215091153 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE interview_category (id INT AUTO_INCREMENT NOT NULL, name LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE interview ADD category_id INT NOT NULL');
        $this->addSql('ALTER TABLE interview ADD CONSTRAINT FK_CF1D3C3412469DE2 FOREIGN KEY (category_id) REFERENCES interview_category (id)');
        $this->addSql('CREATE INDEX IDX_CF1D3C3412469DE2 ON interview (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE interview DROP FOREIGN KEY FK_CF1D3C3412469DE2');
        $this->addSql('DROP TABLE interview_category');
        $this->addSql('DROP INDEX IDX_CF1D3C3412469DE2 ON interview');
        $this->addSql('ALTER TABLE interview DROP category_id');
    }
}
