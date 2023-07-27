<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230727180017 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notes ADD todo_list_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE notes ADD CONSTRAINT FK_11BA68CE8A7DCFA FOREIGN KEY (todo_list_id) REFERENCES todo_lists (id)');
        $this->addSql('CREATE INDEX IDX_11BA68CE8A7DCFA ON notes (todo_list_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notes DROP FOREIGN KEY FK_11BA68CE8A7DCFA');
        $this->addSql('DROP INDEX IDX_11BA68CE8A7DCFA ON notes');
        $this->addSql('ALTER TABLE notes DROP todo_list_id');
    }
}
