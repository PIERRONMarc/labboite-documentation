<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200521185307 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE notice (id INT AUTO_INCREMENT NOT NULL, content LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tool ADD notice_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tool ADD CONSTRAINT FK_20F33ED17D540AB FOREIGN KEY (notice_id) REFERENCES notice (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_20F33ED17D540AB ON tool (notice_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tool DROP FOREIGN KEY FK_20F33ED17D540AB');
        $this->addSql('DROP TABLE notice');
        $this->addSql('DROP INDEX UNIQ_20F33ED17D540AB ON tool');
        $this->addSql('ALTER TABLE tool DROP notice_id');
    }
}
