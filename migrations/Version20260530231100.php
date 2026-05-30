<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260530231100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD total NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE order_item ADD unit_price NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE produit ADD stock INT DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP total');
        $this->addSql('ALTER TABLE order_item DROP unit_price');
        $this->addSql('ALTER TABLE produit DROP stock');
    }
}
