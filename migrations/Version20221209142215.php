<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221209142215 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket_content DROP FOREIGN KEY FK_3A6220B04584665A');
        $this->addSql('DROP INDEX UNIQ_3A6220B04584665A ON basket_content');
        $this->addSql('ALTER TABLE basket_content CHANGE product_id products_id INT NOT NULL');
        $this->addSql('ALTER TABLE basket_content ADD CONSTRAINT FK_3A6220B06C8A81A9 FOREIGN KEY (products_id) REFERENCES products (id)');
        $this->addSql('CREATE INDEX IDX_3A6220B06C8A81A9 ON basket_content (products_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket_content DROP FOREIGN KEY FK_3A6220B06C8A81A9');
        $this->addSql('DROP INDEX IDX_3A6220B06C8A81A9 ON basket_content');
        $this->addSql('ALTER TABLE basket_content CHANGE products_id product_id INT NOT NULL');
        $this->addSql('ALTER TABLE basket_content ADD CONSTRAINT FK_3A6220B04584665A FOREIGN KEY (product_id) REFERENCES products (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3A6220B04584665A ON basket_content (product_id)');
    }
}
