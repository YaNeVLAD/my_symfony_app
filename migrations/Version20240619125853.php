<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240619125853 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket ADD item_count INT NOT NULL;');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket DROP item_count, CHANGE user_id user_id INT UNSIGNED NOT NULL, CHANGE item_id item_id INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE order_copy CHANGE order_id order_id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE categorie categorie VARCHAR(255) DEFAULT NULL, CHANGE name name VARCHAR(255) DEFAULT NULL, CHANGE description description TEXT DEFAULT NULL, CHANGE price price SMALLINT DEFAULT NULL, CHANGE featured featured TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE user_id user_id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE birth_date birth_date DATETIME NOT NULL');
    }
}
