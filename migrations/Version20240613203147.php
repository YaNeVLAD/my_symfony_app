<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240613203147 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE `order_copy` (`order_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, `categorie` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_0900_ai_ci', `name` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_0900_ai_ci', `description` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci', `image_path` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_0900_ai_ci', `price` INT(10) UNSIGNED NOT NULL, `featured` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0', PRIMARY KEY (`order_id`) USING BTREE, UNIQUE INDEX `name_idx` (`name`) USING BTREE) COLLATE='utf8mb4_0900_ai_ci' ENGINE=InnoDB;");
        $this->addSql("INSERT INTO `order_copy` (`order_id`, `categorie`, `name`, `description`, `image_path`, `price`, `featured`) VALUES (1, 'pizza', 'Пицца Фирменная', NULL, 'image1.png', 350, 1);
                       INSERT INTO `order_copy` (`order_id`, `categorie`, `name`, `description`, `image_path`, `price`, `featured`) VALUES (2, 'pizza', 'Пицца Гавайская', NULL, 'image2.png', 400, 0);
                       INSERT INTO `order_copy` (`order_id`, `categorie`, `name`, `description`, `image_path`, `price`, `featured`) VALUES (3, 'pizza', 'Пицца Маргарита', NULL, 'image3.png', 350, 0);
                       INSERT INTO `order_copy` (`order_id`, `categorie`, `name`, `description`, `image_path`, `price`, `featured`) VALUES (4, 'pizza', 'Пицца 4 мяса', NULL, 'image4.png', 600, 0);
                       INSERT INTO `order_copy` (`order_id`, `categorie`, `name`, `description`, `image_path`, `price`, `featured`) VALUES (5, 'pizza', 'Пицца 4 сыра', NULL, 'image5.png', 550, 0);
                       INSERT INTO `order_copy` (`order_id`, `categorie`, `name`, `description`, `image_path`, `price`, `featured`) VALUES (6, 'pizza', 'Пицца Морская', NULL, 'image6.png', 500, 0);
                       INSERT INTO `order_copy` (`order_id`, `categorie`, `name`, `description`, `image_path`, `price`, `featured`) VALUES (7, 'pizza', 'Пицца Грибная', NULL, 'image7.png', 500, 0);
                       INSERT INTO `order_copy` (`order_id`, `categorie`, `name`, `description`, `image_path`, `price`, `featured`) VALUES (8, 'pizza', 'Пицца Охотничья', NULL, 'image8.png', 400, 0);
                       INSERT INTO `order_copy` (`order_id`, `categorie`, `name`, `description`, `image_path`, `price`, `featured`) VALUES (9, 'salad', 'Салат “Греческий”', NULL, 'image9.png', 280, 0);
                       INSERT INTO `order_copy` (`order_id`, `categorie`, `name`, `description`, `image_path`, `price`, `featured`) VALUES (10, 'salad', 'Салат “Цезарь”', NULL, 'image10.png', 350, 0);
                       INSERT INTO `order_copy` (`order_id`, `categorie`, `name`, `description`, `image_path`, `price`, `featured`) VALUES (11, 'salad', 'Салат “Чука”', NULL, 'image11.png', 200, 0);
                       INSERT INTO `order_copy` (`order_id`, `categorie`, `name`, `description`, `image_path`, `price`, `featured`) VALUES (12, 'drink', 'Напитки (в ассортименте)', NULL, 'image12.png', 100, 0);
                       INSERT INTO `order_copy` (`order_id`, `categorie`, `name`, `description`, `image_path`, `price`, `featured`) VALUES (13, 'snack', 'Картофель по-деревенски', NULL, 'image13.png', 180, 0);
                       INSERT INTO `order_copy` (`order_id`, `categorie`, `name`, `description`, `image_path`, `price`, `featured`) VALUES (14, 'snack', 'Куриные Нагетсы', NULL, 'image14.png', 200, 0);
                       INSERT INTO `order_copy` (`order_id`, `categorie`, `name`, `description`, `image_path`, `price`, `featured`) VALUES (15, 'snack', 'Картофель Фри', NULL, 'image15.png', 150, 0);
                       INSERT INTO `order_copy` (`order_id`, `categorie`, `name`, `description`, `image_path`, `price`, `featured`) VALUES (16, 'dessert', 'Яблоко', NULL, 'image16.png', 90, 0);
                       INSERT INTO `order_copy` (`order_id`, `categorie`, `name`, `description`, `image_path`, `price`, `featured`) VALUES (17, 'dessert', 'Банан', NULL, 'image17.png', 100, 0);
                     ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE order');
    }
}
