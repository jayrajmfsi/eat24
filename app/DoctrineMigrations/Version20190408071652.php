<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190408071652 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE restaurant DROP FOREIGN KEY FK_EB95123F8BAC62AF');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6498BAC62AF');
        $this->addSql('CREATE TABLE cuisine (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE address (id INT AUTO_INCREMENT NOT NULL, completeAddress LONGTEXT NOT NULL, geoPoint POINT DEFAULT NULL COMMENT \'(DC2Type:point)\', customer_id INT NOT NULL, address_type VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE restaurant_cuisines (restaurant_id INT NOT NULL, cuisine_id INT NOT NULL, INDEX IDX_E1DB6C18B1E7706E (restaurant_id), INDEX IDX_E1DB6C18ED4BAC14 (cuisine_id), PRIMARY KEY(restaurant_id, cuisine_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE restaurant_cuisines ADD CONSTRAINT FK_E1DB6C18B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE restaurant_cuisines ADD CONSTRAINT FK_E1DB6C18ED4BAC14 FOREIGN KEY (cuisine_id) REFERENCES cuisine (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP INDEX IDX_8D93D6498BAC62AF ON user');
        $this->addSql('ALTER TABLE user DROP city_id, DROP address');
        $this->addSql('DROP INDEX IDX_EB95123F8BAC62AF ON restaurant');
        $this->addSql('ALTER TABLE restaurant ADD name VARCHAR(255) NOT NULL, DROP city_id, DROP address');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE restaurant_cuisines DROP FOREIGN KEY FK_E1DB6C18ED4BAC14');
        $this->addSql('CREATE TABLE city (id INT AUTO_INCREMENT NOT NULL, cityName VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, zipCode VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE cuisine');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE restaurant_cuisines');
        $this->addSql('ALTER TABLE restaurant ADD city_id INT DEFAULT NULL, ADD address LONGTEXT NOT NULL COLLATE utf8_unicode_ci, DROP name');
        $this->addSql('ALTER TABLE restaurant ADD CONSTRAINT FK_EB95123F8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('CREATE INDEX IDX_EB95123F8BAC62AF ON restaurant (city_id)');
        $this->addSql('ALTER TABLE user ADD city_id INT DEFAULT NULL, ADD address LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6498BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6498BAC62AF ON user (city_id)');
    }
}
