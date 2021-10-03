<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210925021223 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE member ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D7597D3FE');
        $this->addSql('DROP INDEX IDX_5A8A6C8D7597D3FE ON post');
        $this->addSql('ALTER TABLE post DROP member_id');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D47597D3FE');
        $this->addSql('DROP INDEX IDX_D044D5D47597D3FE ON session');
        $this->addSql('ALTER TABLE session DROP member_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE member DROP updated_at');
        $this->addSql('ALTER TABLE post ADD member_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D7597D3FE FOREIGN KEY (member_id) REFERENCES member (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D7597D3FE ON post (member_id)');
        $this->addSql('ALTER TABLE session ADD member_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D47597D3FE FOREIGN KEY (member_id) REFERENCES member (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_D044D5D47597D3FE ON session (member_id)');
    }
}
