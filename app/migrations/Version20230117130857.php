<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230117130857 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE interview_category ADD name_slugged LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE profile ADD experiences_from_extract LONGTEXT DEFAULT NULL, CHANGE full_name_slugged full_name_slugged LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE interview_category DROP name_slugged');
        $this->addSql('ALTER TABLE profile DROP experiences_from_extract, CHANGE full_name_slugged full_name_slugged VARCHAR(255) DEFAULT NULL');
    }
}
