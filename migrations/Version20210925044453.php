<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210925044453 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE member ADD posts_id INT DEFAULT NULL, ADD sessions_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE member ADD CONSTRAINT FK_70E4FA78D5E258C5 FOREIGN KEY (posts_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE member ADD CONSTRAINT FK_70E4FA78F17C4D8C FOREIGN KEY (sessions_id) REFERENCES session (id)');
        $this->addSql('CREATE INDEX IDX_70E4FA78D5E258C5 ON member (posts_id)');
        $this->addSql('CREATE INDEX IDX_70E4FA78F17C4D8C ON member (sessions_id)');
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
        $this->addSql('ALTER TABLE member DROP FOREIGN KEY FK_70E4FA78D5E258C5');
        $this->addSql('ALTER TABLE member DROP FOREIGN KEY FK_70E4FA78F17C4D8C');
        $this->addSql('DROP INDEX IDX_70E4FA78D5E258C5 ON member');
        $this->addSql('DROP INDEX IDX_70E4FA78F17C4D8C ON member');
        $this->addSql('ALTER TABLE member DROP posts_id, DROP sessions_id');
        $this->addSql('ALTER TABLE post ADD member_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D7597D3FE FOREIGN KEY (member_id) REFERENCES member (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D7597D3FE ON post (member_id)');
        $this->addSql('ALTER TABLE session ADD member_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D47597D3FE FOREIGN KEY (member_id) REFERENCES member (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_D044D5D47597D3FE ON session (member_id)');
    }
}
