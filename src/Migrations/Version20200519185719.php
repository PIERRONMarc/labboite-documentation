<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200519185719 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE resource ADD tool_id INT NOT NULL');
        $this->addSql('ALTER TABLE resource ADD CONSTRAINT FK_BC91F4168F7B22CC FOREIGN KEY (tool_id) REFERENCES tool (id)');
        $this->addSql('CREATE INDEX IDX_BC91F4168F7B22CC ON resource (tool_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE resource DROP FOREIGN KEY FK_BC91F4168F7B22CC');
        $this->addSql('DROP INDEX IDX_BC91F4168F7B22CC ON resource');
        $this->addSql('ALTER TABLE resource DROP tool_id');
    }
}
