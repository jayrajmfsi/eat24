<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190411094617 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE menu_item ADD is_veg TINYINT(1) DEFAULT \'1\' NOT NULL COMMENT \'0 means non vegitarian, 1 means vegitarian\'');
        $this->addSql('ALTER TABLE in_restaurant CHANGE active active TINYINT(1) DEFAULT \'1\' NOT NULL COMMENT \'0 means inactive, 1 means active\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE in_restaurant CHANGE active active TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE menu_item DROP is_veg');
    }
}
