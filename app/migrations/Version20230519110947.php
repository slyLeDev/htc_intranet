<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230519110947 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE batch_customer CHANGE created_by created_by INT DEFAULT NULL, CHANGE updated_by updated_by INT DEFAULT NULL');
        $this->addSql('ALTER TABLE batch_customer ADD CONSTRAINT FK_AD7E069DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE batch_customer ADD CONSTRAINT FK_AD7E06916FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_AD7E069DE12AB56 ON batch_customer (created_by)');
        $this->addSql('CREATE INDEX IDX_AD7E06916FE72E1 ON batch_customer (updated_by)');
        $this->addSql('ALTER TABLE customer CHANGE created_by created_by INT DEFAULT NULL, CHANGE updated_by updated_by INT DEFAULT NULL');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E09DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E0916FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_81398E09DE12AB56 ON customer (created_by)');
        $this->addSql('CREATE INDEX IDX_81398E0916FE72E1 ON customer (updated_by)');
        $this->addSql('ALTER TABLE deal CHANGE created_by created_by INT DEFAULT NULL, CHANGE updated_by updated_by INT DEFAULT NULL');
        $this->addSql('ALTER TABLE deal ADD CONSTRAINT FK_E3FEC116DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE deal ADD CONSTRAINT FK_E3FEC11616FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_E3FEC116DE12AB56 ON deal (created_by)');
        $this->addSql('CREATE INDEX IDX_E3FEC11616FE72E1 ON deal (updated_by)');
        $this->addSql('ALTER TABLE interview CHANGE created_by created_by INT DEFAULT NULL, CHANGE updated_by updated_by INT DEFAULT NULL');
        $this->addSql('ALTER TABLE interview ADD CONSTRAINT FK_CF1D3C34DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE interview ADD CONSTRAINT FK_CF1D3C3416FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_CF1D3C34DE12AB56 ON interview (created_by)');
        $this->addSql('CREATE INDEX IDX_CF1D3C3416FE72E1 ON interview (updated_by)');
        $this->addSql('ALTER TABLE job_sector CHANGE created_by created_by INT DEFAULT NULL, CHANGE updated_by updated_by INT DEFAULT NULL');
        $this->addSql('ALTER TABLE job_sector ADD CONSTRAINT FK_1BA7F469DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE job_sector ADD CONSTRAINT FK_1BA7F46916FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_1BA7F469DE12AB56 ON job_sector (created_by)');
        $this->addSql('CREATE INDEX IDX_1BA7F46916FE72E1 ON job_sector (updated_by)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE batch_customer DROP FOREIGN KEY FK_AD7E069DE12AB56');
        $this->addSql('ALTER TABLE batch_customer DROP FOREIGN KEY FK_AD7E06916FE72E1');
        $this->addSql('DROP INDEX IDX_AD7E069DE12AB56 ON batch_customer');
        $this->addSql('DROP INDEX IDX_AD7E06916FE72E1 ON batch_customer');
        $this->addSql('ALTER TABLE batch_customer CHANGE created_by created_by VARCHAR(255) DEFAULT NULL, CHANGE updated_by updated_by VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E09DE12AB56');
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E0916FE72E1');
        $this->addSql('DROP INDEX IDX_81398E09DE12AB56 ON customer');
        $this->addSql('DROP INDEX IDX_81398E0916FE72E1 ON customer');
        $this->addSql('ALTER TABLE customer CHANGE created_by created_by VARCHAR(255) DEFAULT NULL, CHANGE updated_by updated_by VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE deal DROP FOREIGN KEY FK_E3FEC116DE12AB56');
        $this->addSql('ALTER TABLE deal DROP FOREIGN KEY FK_E3FEC11616FE72E1');
        $this->addSql('DROP INDEX IDX_E3FEC116DE12AB56 ON deal');
        $this->addSql('DROP INDEX IDX_E3FEC11616FE72E1 ON deal');
        $this->addSql('ALTER TABLE deal CHANGE created_by created_by VARCHAR(255) DEFAULT NULL, CHANGE updated_by updated_by VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE interview DROP FOREIGN KEY FK_CF1D3C34DE12AB56');
        $this->addSql('ALTER TABLE interview DROP FOREIGN KEY FK_CF1D3C3416FE72E1');
        $this->addSql('DROP INDEX IDX_CF1D3C34DE12AB56 ON interview');
        $this->addSql('DROP INDEX IDX_CF1D3C3416FE72E1 ON interview');
        $this->addSql('ALTER TABLE interview CHANGE created_by created_by VARCHAR(255) DEFAULT NULL, CHANGE updated_by updated_by VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE job_sector DROP FOREIGN KEY FK_1BA7F469DE12AB56');
        $this->addSql('ALTER TABLE job_sector DROP FOREIGN KEY FK_1BA7F46916FE72E1');
        $this->addSql('DROP INDEX IDX_1BA7F469DE12AB56 ON job_sector');
        $this->addSql('DROP INDEX IDX_1BA7F46916FE72E1 ON job_sector');
        $this->addSql('ALTER TABLE job_sector CHANGE created_by created_by VARCHAR(255) DEFAULT NULL, CHANGE updated_by updated_by VARCHAR(255) DEFAULT NULL');
    }
}
