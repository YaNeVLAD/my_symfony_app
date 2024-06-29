<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240622082054 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<SQL
                            ALTER TABLE user 
                            ADD password VARCHAR(255) NOT NULL, 
                            ADD role INT NOT NULL, 
                            CHANGE birth_date birth_date DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)'
                        SQL);
        $this->addSql(<<<SQL
                            UPDATE `user` 
                            SET 
                            `user_id`=1, 
                            `first_name`='Владислав', 
                            `last_name`='Ковалев', 
                            `middle_name`='Витальевич', 
                            `gender`='Мужчина', 
                            `birth_date`='2003-11-15 17:45:06', 
                            `email`='vladislav.kovalev@ispring.institute', 
                            `phone`=NULL, 
                            `avatar_path`='image1.jpg', 
                            `password`='29d2469566519a672998ab2851afd877', 
                            `role`=2 
                            WHERE `user_id`=1;
                        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket CHANGE user_id user_id INT UNSIGNED NOT NULL, CHANGE item_id item_id INT UNSIGNED NOT NULL');
        $this->addSql('DROP INDEX name_idx ON order_copy');
        $this->addSql('ALTER TABLE order_copy CHANGE order_id order_id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE description description TEXT DEFAULT NULL, CHANGE price price INT UNSIGNED NOT NULL, CHANGE featured featured TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX name_idx ON order_copy (name)');
        $this->addSql('DROP INDEX email_idx ON user');
        $this->addSql('DROP INDEX phone_idx ON user');
        $this->addSql('ALTER TABLE user DROP password, DROP role, CHANGE user_id user_id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE birth_date birth_date DATETIME NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX email_idx ON user (email)');
        $this->addSql('CREATE UNIQUE INDEX phone_idx ON user (phone)');
    }
}
