<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200914124823 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE session_content (id INT AUTO_INCREMENT NOT NULL, session_id INT NOT NULL, ip VARCHAR(255) DEFAULT NULL, created DATETIME NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_EC1DB2F3613FECDF (session_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chat_content (id INT NOT NULL, message LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE session_content ADD CONSTRAINT FK_EC1DB2F3613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE chat_content ADD CONSTRAINT FK_B2CBFCC0BF396750 FOREIGN KEY (id) REFERENCES session_content (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chat_content DROP FOREIGN KEY FK_B2CBFCC0BF396750');
        $this->addSql('DROP TABLE chat_content');
        $this->addSql('DROP TABLE session_content');
    }
}
