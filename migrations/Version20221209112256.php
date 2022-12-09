<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221209112256 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket_content ADD basket_id INT NOT NULL');
        $this->addSql('ALTER TABLE basket_content ADD CONSTRAINT FK_3A6220B01BE1FB52 FOREIGN KEY (basket_id) REFERENCES basket (id)');
        $this->addSql('CREATE INDEX IDX_3A6220B01BE1FB52 ON basket_content (basket_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket_content DROP FOREIGN KEY FK_3A6220B01BE1FB52');
        $this->addSql('DROP INDEX IDX_3A6220B01BE1FB52 ON basket_content');
        $this->addSql('ALTER TABLE basket_content DROP basket_id');
    }
}
