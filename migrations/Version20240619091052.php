<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240619091052 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket DROP FOREIGN KEY FK_2246507B550872C');
        $this->addSql('ALTER TABLE basket DROP FOREIGN KEY FK_2246507B96133AFD');
        $this->addSql('DROP INDEX IDX_2246507B550872C ON basket');
        $this->addSql('DROP INDEX IDX_2246507B96133AFD ON basket');
        $this->addSql('ALTER TABLE basket ADD user_id INT UNSIGNED NOT NULL, ADD item_id INT UNSIGNED NOT NULL, DROP user_email, DROP item_name');
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507BA76ED395 FOREIGN KEY (user_id) REFERENCES user (user_id)');
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507B126F525E FOREIGN KEY (item_id) REFERENCES order_copy (order_id)');
        $this->addSql('CREATE INDEX IDX_2246507BA76ED395 ON basket (user_id)');
        $this->addSql('CREATE INDEX IDX_2246507B126F525E ON basket (item_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket DROP FOREIGN KEY FK_2246507BA76ED395');
        $this->addSql('ALTER TABLE basket DROP FOREIGN KEY FK_2246507B126F525E');
        $this->addSql('DROP INDEX IDX_2246507BA76ED395 ON basket');
        $this->addSql('DROP INDEX IDX_2246507B126F525E ON basket');
        $this->addSql('ALTER TABLE basket ADD user_email VARCHAR(255) NOT NULL, ADD item_name VARCHAR(255) NOT NULL, DROP user_id, DROP item_id');
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507B550872C FOREIGN KEY (user_email) REFERENCES user (email) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507B96133AFD FOREIGN KEY (item_name) REFERENCES order_copy (name) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_2246507B550872C ON basket (user_email)');
        $this->addSql('CREATE INDEX IDX_2246507B96133AFD ON basket (item_name)');
        $this->addSql('DROP INDEX name_idx ON order_copy');
        $this->addSql('ALTER TABLE order_copy CHANGE order_id order_id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE categorie categorie VARCHAR(255) DEFAULT NULL, CHANGE name name VARCHAR(255) DEFAULT NULL, CHANGE description description TEXT DEFAULT NULL, CHANGE price price SMALLINT DEFAULT NULL, CHANGE featured featured TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX name_idx ON order_copy (name)');
        $this->addSql('DROP INDEX email_idx ON user');
        $this->addSql('DROP INDEX phone_idx ON user');
        $this->addSql('ALTER TABLE user CHANGE user_id user_id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE birth_date birth_date DATETIME NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX email_idx ON user (email)');
        $this->addSql('CREATE UNIQUE INDEX phone_idx ON user (phone)');
    }
}
