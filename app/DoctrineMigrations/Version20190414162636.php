<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190414162636 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE order_status DROP FOREIGN KEY FK_B88F75C9CD6F827A');
        $this->addSql('DROP TABLE order_status');
        $this->addSql('DROP TABLE status_catalog');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C47D7D8EA');
        $this->addSql('DROP INDEX IDX_9474526C47D7D8EA ON comment');
        $this->addSql('ALTER TABLE comment DROP placed_order_id');
        $this->addSql('ALTER TABLE placed_order ADD order_comment INT DEFAULT NULL, ADD order_reference BIGINT NOT NULL, DROP deliveryAddress, DROP comment, CHANGE estimatedDeliveryTime estimatedDeliveryTime DATETIME DEFAULT NULL, CHANGE actualDeliveryTime actualDeliveryTime DATETIME DEFAULT NULL, CHANGE totalPrice totalPrice NUMERIC(12, 2) DEFAULT NULL, CHANGE ordertime created_date_time DATETIME NOT NULL');
        $this->addSql('ALTER TABLE placed_order ADD CONSTRAINT FK_77BB89C5620EFB27 FOREIGN KEY (order_comment) REFERENCES comment (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_77BB89C5620EFB27 ON placed_order (order_comment)');
        $this->addSql('ALTER TABLE address CHANGE completeaddress complete_address LONGTEXT NOT NULL, CHANGE geopoint geo_point POINT DEFAULT NULL COMMENT \'(DC2Type:point)\'');
        $this->addSql('ALTER TABLE in_order DROP INDEX IDX_BFDC8DE69AB44FE0, ADD UNIQUE INDEX UNIQ_BFDC8DE69AB44FE0 (menu_item_id)');
        $this->addSql('ALTER TABLE in_order DROP FOREIGN KEY FK_BFDC8DE69AB44FE0');
        $this->addSql('ALTER TABLE in_order ADD CONSTRAINT FK_BFDC8DE69AB44FE0 FOREIGN KEY (menu_item_id) REFERENCES in_restaurant (id)');
        $this->addSql('ALTER TABLE in_restaurant ADD item_reference VARCHAR(255) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE order_status (id INT AUTO_INCREMENT NOT NULL, status_catalog_id INT DEFAULT NULL, ts DATETIME NOT NULL, INDEX IDX_B88F75C9CD6F827A (status_catalog_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE status_catalog (id INT AUTO_INCREMENT NOT NULL, statusName VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE order_status ADD CONSTRAINT FK_B88F75C9CD6F827A FOREIGN KEY (status_catalog_id) REFERENCES status_catalog (id)');
        $this->addSql('ALTER TABLE address CHANGE complete_address completeAddress LONGTEXT NOT NULL COLLATE utf8_unicode_ci, CHANGE geo_point geoPoint POINT DEFAULT NULL COMMENT \'(DC2Type:point)\'');
        $this->addSql('ALTER TABLE comment ADD placed_order_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C47D7D8EA FOREIGN KEY (placed_order_id) REFERENCES placed_order (id)');
        $this->addSql('CREATE INDEX IDX_9474526C47D7D8EA ON comment (placed_order_id)');
        $this->addSql('ALTER TABLE in_order DROP INDEX UNIQ_BFDC8DE69AB44FE0, ADD INDEX IDX_BFDC8DE69AB44FE0 (menu_item_id)');
        $this->addSql('ALTER TABLE in_order DROP FOREIGN KEY FK_BFDC8DE69AB44FE0');
        $this->addSql('ALTER TABLE in_order ADD CONSTRAINT FK_BFDC8DE69AB44FE0 FOREIGN KEY (menu_item_id) REFERENCES menu_item (id)');
        $this->addSql('ALTER TABLE in_restaurant DROP item_reference');
        $this->addSql('ALTER TABLE placed_order DROP FOREIGN KEY FK_77BB89C5620EFB27');
        $this->addSql('DROP INDEX UNIQ_77BB89C5620EFB27 ON placed_order');
        $this->addSql('ALTER TABLE placed_order ADD deliveryAddress VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD comment LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, DROP order_comment, DROP order_reference, CHANGE estimatedDeliveryTime estimatedDeliveryTime DATETIME NOT NULL, CHANGE actualDeliveryTime actualDeliveryTime DATETIME NOT NULL, CHANGE totalPrice totalPrice NUMERIC(12, 2) NOT NULL, CHANGE created_date_time orderTime DATETIME NOT NULL');
    }
}
