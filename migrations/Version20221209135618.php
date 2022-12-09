<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221209135618 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket_content ADD CONSTRAINT FK_3A6220B04584665A FOREIGN KEY (product_id) REFERENCES products (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3A6220B04584665A ON basket_content (product_id)');
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5A95271FCC');
        $this->addSql('DROP INDEX IDX_B3BA5A5A95271FCC ON products');
        $this->addSql('ALTER TABLE products DROP basket_content_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket_content DROP FOREIGN KEY FK_3A6220B04584665A');
        $this->addSql('DROP INDEX UNIQ_3A6220B04584665A ON basket_content');
        $this->addSql('ALTER TABLE products ADD basket_content_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A95271FCC FOREIGN KEY (basket_content_id) REFERENCES basket_content (id)');
        $this->addSql('CREATE INDEX IDX_B3BA5A5A95271FCC ON products (basket_content_id)');
    }
}
