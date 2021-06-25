<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210625180657 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE agent ADD entity_id INT DEFAULT NULL, ADD direction_id INT DEFAULT NULL, ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE agent ADD CONSTRAINT FK_268B9C9D81257D5D FOREIGN KEY (entity_id) REFERENCES entity (id)');
        $this->addSql('ALTER TABLE agent ADD CONSTRAINT FK_268B9C9DAF73D997 FOREIGN KEY (direction_id) REFERENCES direction (id)');
        $this->addSql('ALTER TABLE agent ADD CONSTRAINT FK_268B9C9DAE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('CREATE INDEX IDX_268B9C9D81257D5D ON agent (entity_id)');
        $this->addSql('CREATE INDEX IDX_268B9C9DAF73D997 ON agent (direction_id)');
        $this->addSql('CREATE INDEX IDX_268B9C9DAE80F5DF ON agent (department_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE agent DROP FOREIGN KEY FK_268B9C9D81257D5D');
        $this->addSql('ALTER TABLE agent DROP FOREIGN KEY FK_268B9C9DAF73D997');
        $this->addSql('ALTER TABLE agent DROP FOREIGN KEY FK_268B9C9DAE80F5DF');
        $this->addSql('DROP INDEX IDX_268B9C9D81257D5D ON agent');
        $this->addSql('DROP INDEX IDX_268B9C9DAF73D997 ON agent');
        $this->addSql('DROP INDEX IDX_268B9C9DAE80F5DF ON agent');
        $this->addSql('ALTER TABLE agent DROP entity_id, DROP direction_id, DROP department_id');
    }
}
