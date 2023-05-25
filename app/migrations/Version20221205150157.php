<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221205150157 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE interview ADD consultant_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE interview ADD CONSTRAINT FK_CF1D3C3444F779A2 FOREIGN KEY (consultant_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_CF1D3C3444F779A2 ON interview (consultant_id)');
        $this->addSql('ALTER TABLE profile ADD blacklisted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE interview DROP FOREIGN KEY FK_CF1D3C3444F779A2');
        $this->addSql('DROP INDEX IDX_CF1D3C3444F779A2 ON interview');
        $this->addSql('ALTER TABLE interview DROP consultant_id');
        $this->addSql('ALTER TABLE profile DROP blacklisted_at');
    }
}
