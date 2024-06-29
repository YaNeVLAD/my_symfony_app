<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240525070639 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE `user` (`user_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, `first_name` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_0900_ai_ci', `last_name` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_0900_ai_ci', `middle_name` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci', `gender` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_0900_ai_ci', `birth_date` DATETIME NOT NULL, `email` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_0900_ai_ci', `phone` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci', `avatar_path` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci', PRIMARY KEY (`user_id`) USING BTREE) COLLATE='utf8mb4_0900_ai_ci' ENGINE=InnoDB;");
        $this->addSql('CREATE UNIQUE INDEX email_idx ON user (email)');
        $this->addSql('CREATE UNIQUE INDEX phone_idx ON user (phone)');
        $this->addSql("INSERT INTO `user` (`user_id`, `first_name`, `last_name`, `middle_name`, `gender`, `birth_date`, `email`, `phone`, `avatar_path`) VALUES (1, 'Владислав', 'Ковалев', 'Витальевич', 'Мужчина', '2003-11-15 17:45:06', 'vladislav.kovalev@ispring.institute', NULL, 'image1.jpg');");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP INDEX email_idx ON user');
        $this->addSql('DROP INDEX phone_idx ON user');
        $this->addSql('ALTER TABLE user CHANGE birth_date birth_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('DROP DATABASE php_course');
    }
}
