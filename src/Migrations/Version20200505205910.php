<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200505205910 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, theme_id INT NOT NULL, name VARCHAR(255) NOT NULL, display_order INT NOT NULL, thumbnail_name VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) NOT NULL, INDEX IDX_64C19C159027487 (theme_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE characteristic (id INT AUTO_INCREMENT NOT NULL, tool_id INT NOT NULL, content VARCHAR(255) NOT NULL, INDEX IDX_522FA9508F7B22CC (tool_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE consumable (id INT AUTO_INCREMENT NOT NULL, tool_id INT NOT NULL, name VARCHAR(255) NOT NULL, picture_name VARCHAR(255) DEFAULT NULL, description LONGTEXT NOT NULL, price DOUBLE PRECISION NOT NULL, charge LONGTEXT NOT NULL, INDEX IDX_4475F0958F7B22CC (tool_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feedback (id INT AUTO_INCREMENT NOT NULL, content LONGTEXT NOT NULL, date DATETIME NOT NULL, mail VARCHAR(255) NOT NULL, author VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE information (id INT AUTO_INCREMENT NOT NULL, tool_id INT NOT NULL, picture_name VARCHAR(255) DEFAULT NULL, content LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_297918838F7B22CC (tool_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notice_paragraph (id INT AUTO_INCREMENT NOT NULL, tool_id INT NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, INDEX IDX_34E18F648F7B22CC (tool_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, tool_id INT NOT NULL, answer LONGTEXT NOT NULL, question VARCHAR(255) NOT NULL, INDEX IDX_B6F7494E8F7B22CC (tool_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE theme (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tip (id INT AUTO_INCREMENT NOT NULL, tool_id INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, picture_name VARCHAR(255) DEFAULT NULL, youtube_link VARCHAR(255) DEFAULT NULL, INDEX IDX_4883B84C8F7B22CC (tool_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tool (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, name VARCHAR(255) NOT NULL, picture_name VARCHAR(255) DEFAULT NULL, description LONGTEXT NOT NULL, type VARCHAR(255) NOT NULL, display_order INT NOT NULL, slug VARCHAR(255) NOT NULL, INDEX IDX_20F33ED112469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tutorial (id INT AUTO_INCREMENT NOT NULL, tool_id INT NOT NULL, youtube_link VARCHAR(255) NOT NULL, INDEX IDX_C66BFFE98F7B22CC (tool_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, mail VARCHAR(255) DEFAULT NULL, registration_date DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C159027487 FOREIGN KEY (theme_id) REFERENCES theme (id)');
        $this->addSql('ALTER TABLE characteristic ADD CONSTRAINT FK_522FA9508F7B22CC FOREIGN KEY (tool_id) REFERENCES tool (id)');
        $this->addSql('ALTER TABLE consumable ADD CONSTRAINT FK_4475F0958F7B22CC FOREIGN KEY (tool_id) REFERENCES tool (id)');
        $this->addSql('ALTER TABLE information ADD CONSTRAINT FK_297918838F7B22CC FOREIGN KEY (tool_id) REFERENCES tool (id)');
        $this->addSql('ALTER TABLE notice_paragraph ADD CONSTRAINT FK_34E18F648F7B22CC FOREIGN KEY (tool_id) REFERENCES tool (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E8F7B22CC FOREIGN KEY (tool_id) REFERENCES tool (id)');
        $this->addSql('ALTER TABLE tip ADD CONSTRAINT FK_4883B84C8F7B22CC FOREIGN KEY (tool_id) REFERENCES tool (id)');
        $this->addSql('ALTER TABLE tool ADD CONSTRAINT FK_20F33ED112469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE tutorial ADD CONSTRAINT FK_C66BFFE98F7B22CC FOREIGN KEY (tool_id) REFERENCES tool (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tool DROP FOREIGN KEY FK_20F33ED112469DE2');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C159027487');
        $this->addSql('ALTER TABLE characteristic DROP FOREIGN KEY FK_522FA9508F7B22CC');
        $this->addSql('ALTER TABLE consumable DROP FOREIGN KEY FK_4475F0958F7B22CC');
        $this->addSql('ALTER TABLE information DROP FOREIGN KEY FK_297918838F7B22CC');
        $this->addSql('ALTER TABLE notice_paragraph DROP FOREIGN KEY FK_34E18F648F7B22CC');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E8F7B22CC');
        $this->addSql('ALTER TABLE tip DROP FOREIGN KEY FK_4883B84C8F7B22CC');
        $this->addSql('ALTER TABLE tutorial DROP FOREIGN KEY FK_C66BFFE98F7B22CC');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE characteristic');
        $this->addSql('DROP TABLE consumable');
        $this->addSql('DROP TABLE feedback');
        $this->addSql('DROP TABLE information');
        $this->addSql('DROP TABLE notice_paragraph');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE theme');
        $this->addSql('DROP TABLE tip');
        $this->addSql('DROP TABLE tool');
        $this->addSql('DROP TABLE tutorial');
        $this->addSql('DROP TABLE user');
    }
}
