<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250917135144 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE foods (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE food_user (user_id INT NOT NULL, food_id INT NOT NULL, INDEX IDX_38B47E98A76ED395 (user_id), INDEX IDX_38B47E98BA8E87C4 (food_id), PRIMARY KEY (user_id, food_id))');
        $this->addSql('ALTER TABLE food_user ADD CONSTRAINT FK_38B47E98A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE food_user ADD CONSTRAINT FK_38B47E98BA8E87C4 FOREIGN KEY (food_id) REFERENCES foods (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE food_user DROP FOREIGN KEY FK_38B47E98A76ED395');
        $this->addSql('ALTER TABLE food_user DROP FOREIGN KEY FK_38B47E98BA8E87C4');
        $this->addSql('DROP TABLE foods');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE food_user');
    }
}
