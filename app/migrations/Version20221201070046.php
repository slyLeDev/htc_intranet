<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221201070046 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE timeline_experiences ADD profile_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE timeline_experiences ADD CONSTRAINT FK_F1C155F2CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)');
        $this->addSql('CREATE INDEX IDX_F1C155F2CCFA12B8 ON timeline_experiences (profile_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE timeline_experiences DROP FOREIGN KEY FK_F1C155F2CCFA12B8');
        $this->addSql('DROP INDEX IDX_F1C155F2CCFA12B8 ON timeline_experiences');
        $this->addSql('ALTER TABLE timeline_experiences DROP profile_id');
    }
}
