<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220420085754 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE data_from_sensor ADD local_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE data_from_sensor ADD CONSTRAINT FK_CE77332A5D5A2101 FOREIGN KEY (local_id) REFERENCES local (id)');
        $this->addSql('CREATE INDEX IDX_CE77332A5D5A2101 ON data_from_sensor (local_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE data_from_sensor DROP FOREIGN KEY FK_CE77332A5D5A2101');
        $this->addSql('DROP INDEX IDX_CE77332A5D5A2101 ON data_from_sensor');
        $this->addSql('ALTER TABLE data_from_sensor DROP local_id');
    }
}
