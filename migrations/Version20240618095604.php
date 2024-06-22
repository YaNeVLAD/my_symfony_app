<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240618095604 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE basket (basket_id INT AUTO_INCREMENT NOT NULL, user_email VARCHAR(255) NOT NULL, item_name VARCHAR(255) NOT NULL, INDEX IDX_2246507B550872C (user_email), INDEX IDX_2246507B96133AFD (item_name), PRIMARY KEY(basket_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507B550872C FOREIGN KEY (user_email) REFERENCES user (email)');
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507B96133AFD FOREIGN KEY (item_name) REFERENCES order_copy (name)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket DROP FOREIGN KEY FK_2246507B550872C');
        $this->addSql('ALTER TABLE basket DROP FOREIGN KEY FK_2246507B96133AFD');
        $this->addSql('DROP TABLE basket');
        $this->addSql('DROP INDEX name_idx ON order_copy');
        $this->addSql('ALTER TABLE order_copy CHANGE order_id order_id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE categorie categorie VARCHAR(255) DEFAULT NULL, CHANGE name name VARCHAR(255) DEFAULT NULL, CHANGE description description TEXT DEFAULT NULL, CHANGE price price SMALLINT DEFAULT NULL, CHANGE featured featured TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX name_idx ON order_copy (name)');
        $this->addSql('ALTER TABLE user CHANGE user_id user_id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE birth_date birth_date DATETIME NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX email_idx ON user (email)');
        $this->addSql('CREATE UNIQUE INDEX phone_idx ON user (phone)');
    }
}
