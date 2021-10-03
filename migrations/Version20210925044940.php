<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210925044940 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE member DROP FOREIGN KEY FK_70E4FA78D5E258C5');
        $this->addSql('ALTER TABLE member DROP FOREIGN KEY FK_70E4FA78F17C4D8C');
        $this->addSql('DROP INDEX IDX_70E4FA78D5E258C5 ON member');
        $this->addSql('DROP INDEX IDX_70E4FA78F17C4D8C ON member');
        $this->addSql('ALTER TABLE member DROP posts_id, DROP sessions_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE member ADD posts_id INT DEFAULT NULL, ADD sessions_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE member ADD CONSTRAINT FK_70E4FA78D5E258C5 FOREIGN KEY (posts_id) REFERENCES post (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE member ADD CONSTRAINT FK_70E4FA78F17C4D8C FOREIGN KEY (sessions_id) REFERENCES session (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_70E4FA78D5E258C5 ON member (posts_id)');
        $this->addSql('CREATE INDEX IDX_70E4FA78F17C4D8C ON member (sessions_id)');
    }
}
