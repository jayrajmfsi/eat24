<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190329121921 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, customer_id INT DEFAULT NULL, placed_order_id INT DEFAULT NULL, commentText LONGTEXT NOT NULL, isComplaint TINYINT(1) DEFAULT NULL, isPraise TINYINT(1) DEFAULT NULL, INDEX IDX_9474526C9395C3F3 (customer_id), INDEX IDX_9474526C47D7D8EA (placed_order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE placed_order (id INT AUTO_INCREMENT NOT NULL, customer_id INT DEFAULT NULL, restaurant_id INT DEFAULT NULL, orderTime DATETIME NOT NULL, estimatedDeliveryTime DATETIME NOT NULL, actualDeliveryTime DATETIME NOT NULL, deliveryAddress VARCHAR(255) NOT NULL, totalPrice NUMERIC(12, 2) NOT NULL, discount NUMERIC(12, 2) DEFAULT NULL, finalPrice NUMERIC(12, 2) NOT NULL, comment LONGTEXT DEFAULT NULL, INDEX IDX_77BB89C59395C3F3 (customer_id), INDEX IDX_77BB89C5B1E7706E (restaurant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE city (id INT AUTO_INCREMENT NOT NULL, cityName VARCHAR(255) NOT NULL, zipCode VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer (id INT AUTO_INCREMENT NOT NULL, city_id INT DEFAULT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', address LONGTEXT NOT NULL, phone_number BIGINT NOT NULL, UNIQUE INDEX UNIQ_81398E0992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_81398E09A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_81398E09C05FB297 (confirmation_token), INDEX IDX_81398E098BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE menu_item (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_D754D55012469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE status_catalog (id INT AUTO_INCREMENT NOT NULL, statusName VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_status (id INT AUTO_INCREMENT NOT NULL, status_catalog_id INT DEFAULT NULL, ts DATETIME NOT NULL, INDEX IDX_B88F75C9CD6F827A (status_catalog_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE in_order (id INT AUTO_INCREMENT NOT NULL, placed_order_id INT DEFAULT NULL, menu_item_id INT DEFAULT NULL, quantity INT NOT NULL, itemPrice NUMERIC(12, 2) NOT NULL, INDEX IDX_BFDC8DE647D7D8EA (placed_order_id), INDEX IDX_BFDC8DE69AB44FE0 (menu_item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE in_restaurant (id INT AUTO_INCREMENT NOT NULL, menu_item_id INT DEFAULT NULL, restaurant_id INT DEFAULT NULL, price NUMERIC(12, 2) NOT NULL, active TINYINT(1) NOT NULL, description VARCHAR(255) DEFAULT NULL, INDEX IDX_A4287A899AB44FE0 (menu_item_id), INDEX IDX_A4287A89B1E7706E (restaurant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE restaurant (id INT AUTO_INCREMENT NOT NULL, city_id INT DEFAULT NULL, address LONGTEXT NOT NULL, INDEX IDX_EB95123F8BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C47D7D8EA FOREIGN KEY (placed_order_id) REFERENCES placed_order (id)');
        $this->addSql('ALTER TABLE placed_order ADD CONSTRAINT FK_77BB89C59395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE placed_order ADD CONSTRAINT FK_77BB89C5B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E098BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE menu_item ADD CONSTRAINT FK_D754D55012469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE order_status ADD CONSTRAINT FK_B88F75C9CD6F827A FOREIGN KEY (status_catalog_id) REFERENCES status_catalog (id)');
        $this->addSql('ALTER TABLE in_order ADD CONSTRAINT FK_BFDC8DE647D7D8EA FOREIGN KEY (placed_order_id) REFERENCES placed_order (id)');
        $this->addSql('ALTER TABLE in_order ADD CONSTRAINT FK_BFDC8DE69AB44FE0 FOREIGN KEY (menu_item_id) REFERENCES menu_item (id)');
        $this->addSql('ALTER TABLE in_restaurant ADD CONSTRAINT FK_A4287A899AB44FE0 FOREIGN KEY (menu_item_id) REFERENCES menu_item (id)');
        $this->addSql('ALTER TABLE in_restaurant ADD CONSTRAINT FK_A4287A89B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)');
        $this->addSql('ALTER TABLE restaurant ADD CONSTRAINT FK_EB95123F8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE menu_item DROP FOREIGN KEY FK_D754D55012469DE2');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C47D7D8EA');
        $this->addSql('ALTER TABLE in_order DROP FOREIGN KEY FK_BFDC8DE647D7D8EA');
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E098BAC62AF');
        $this->addSql('ALTER TABLE restaurant DROP FOREIGN KEY FK_EB95123F8BAC62AF');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C9395C3F3');
        $this->addSql('ALTER TABLE placed_order DROP FOREIGN KEY FK_77BB89C59395C3F3');
        $this->addSql('ALTER TABLE in_order DROP FOREIGN KEY FK_BFDC8DE69AB44FE0');
        $this->addSql('ALTER TABLE in_restaurant DROP FOREIGN KEY FK_A4287A899AB44FE0');
        $this->addSql('ALTER TABLE order_status DROP FOREIGN KEY FK_B88F75C9CD6F827A');
        $this->addSql('ALTER TABLE placed_order DROP FOREIGN KEY FK_77BB89C5B1E7706E');
        $this->addSql('ALTER TABLE in_restaurant DROP FOREIGN KEY FK_A4287A89B1E7706E');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE placed_order');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE menu_item');
        $this->addSql('DROP TABLE status_catalog');
        $this->addSql('DROP TABLE order_status');
        $this->addSql('DROP TABLE in_order');
        $this->addSql('DROP TABLE in_restaurant');
        $this->addSql('DROP TABLE restaurant');
    }
}
