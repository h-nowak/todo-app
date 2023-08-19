<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230819094523 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE todo_lists ADD author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE todo_lists ADD CONSTRAINT FK_85714336F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_85714336F675F31B ON todo_lists (author_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE todo_lists DROP FOREIGN KEY FK_85714336F675F31B');
        $this->addSql('DROP INDEX IDX_85714336F675F31B ON todo_lists');
        $this->addSql('ALTER TABLE todo_lists DROP author_id');
    }
}
