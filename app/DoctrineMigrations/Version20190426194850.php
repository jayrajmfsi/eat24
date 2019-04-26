<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190426194850 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE placed_order DROP FOREIGN KEY FK_77BB89C5620EFB27');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP INDEX UNIQ_77BB89C5620EFB27 ON placed_order');
        $this->addSql('ALTER TABLE placed_order DROP order_comment');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, commentText LONGTEXT NOT NULL COLLATE utf8_unicode_ci, isComplaint TINYINT(1) DEFAULT NULL, isPraise TINYINT(1) DEFAULT NULL, INDEX IDX_9474526CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE placed_order ADD order_comment INT DEFAULT NULL');
        $this->addSql('ALTER TABLE placed_order ADD CONSTRAINT FK_77BB89C5620EFB27 FOREIGN KEY (order_comment) REFERENCES comment (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_77BB89C5620EFB27 ON placed_order (order_comment)');
    }
}
