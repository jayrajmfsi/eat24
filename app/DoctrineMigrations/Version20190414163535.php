<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190414163535 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE placed_order ADD address_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE placed_order ADD CONSTRAINT FK_77BB89C5F5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('CREATE INDEX IDX_77BB89C5F5B7AF75 ON placed_order (address_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE placed_order DROP FOREIGN KEY FK_77BB89C5F5B7AF75');
        $this->addSql('DROP INDEX IDX_77BB89C5F5B7AF75 ON placed_order');
        $this->addSql('ALTER TABLE placed_order DROP address_id');
    }
}
