<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221025174723 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE batch_customer_profile (batch_customer_id INT NOT NULL, profile_id INT NOT NULL, INDEX IDX_7C9062C3678E810E (batch_customer_id), INDEX IDX_7C9062C3CCFA12B8 (profile_id), PRIMARY KEY(batch_customer_id, profile_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE batch_customer_profile ADD CONSTRAINT FK_7C9062C3678E810E FOREIGN KEY (batch_customer_id) REFERENCES batch_customer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE batch_customer_profile ADD CONSTRAINT FK_7C9062C3CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE batch_customer_profile DROP FOREIGN KEY FK_7C9062C3678E810E');
        $this->addSql('ALTER TABLE batch_customer_profile DROP FOREIGN KEY FK_7C9062C3CCFA12B8');
        $this->addSql('DROP TABLE batch_customer_profile');
    }
}
