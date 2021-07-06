<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210706110307 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE phone ADD util_number_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE phone ADD CONSTRAINT FK_444F97DDB4B8F7E0 FOREIGN KEY (util_number_id) REFERENCES util_number (id)');
        $this->addSql('CREATE INDEX IDX_444F97DDB4B8F7E0 ON phone (util_number_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE phone DROP FOREIGN KEY FK_444F97DDB4B8F7E0');
        $this->addSql('DROP INDEX IDX_444F97DDB4B8F7E0 ON phone');
        $this->addSql('ALTER TABLE phone DROP util_number_id');
    }
}
