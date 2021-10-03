<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210925045055 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE member_post (member_id INT NOT NULL, post_id INT NOT NULL, INDEX IDX_A60240D17597D3FE (member_id), INDEX IDX_A60240D14B89032C (post_id), PRIMARY KEY(member_id, post_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE member_session (member_id INT NOT NULL, session_id INT NOT NULL, INDEX IDX_6FB062407597D3FE (member_id), INDEX IDX_6FB06240613FECDF (session_id), PRIMARY KEY(member_id, session_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE member_post ADD CONSTRAINT FK_A60240D17597D3FE FOREIGN KEY (member_id) REFERENCES member (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE member_post ADD CONSTRAINT FK_A60240D14B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE member_session ADD CONSTRAINT FK_6FB062407597D3FE FOREIGN KEY (member_id) REFERENCES member (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE member_session ADD CONSTRAINT FK_6FB06240613FECDF FOREIGN KEY (session_id) REFERENCES session (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE member_post');
        $this->addSql('DROP TABLE member_session');
    }
}
