<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220420090222 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE data_from_sensor ADD type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE data_from_sensor ADD CONSTRAINT FK_CE77332AC54C8C93 FOREIGN KEY (type_id) REFERENCES data_type (id)');
        $this->addSql('CREATE INDEX IDX_CE77332AC54C8C93 ON data_from_sensor (type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE data_from_sensor DROP FOREIGN KEY FK_CE77332AC54C8C93');
        $this->addSql('DROP INDEX IDX_CE77332AC54C8C93 ON data_from_sensor');
        $this->addSql('ALTER TABLE data_from_sensor DROP type_id');
    }
}
