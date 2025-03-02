<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250302152257 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE clothing_items_categories (clothing_item_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_DE3AA3CCAA13B545 (clothing_item_id), INDEX IDX_DE3AA3CC12469DE2 (category_id), PRIMARY KEY(clothing_item_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE clothing_items_categories ADD CONSTRAINT FK_DE3AA3CCAA13B545 FOREIGN KEY (clothing_item_id) REFERENCES clothing_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE clothing_items_categories ADD CONSTRAINT FK_DE3AA3CC12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE clothing_item DROP category_id');
        $this->addSql('ALTER TABLE clothing_item ADD CONSTRAINT FK_CFE0A4E944F5D008 FOREIGN KEY (brand_id) REFERENCES partner (id)');
        $this->addSql('CREATE INDEX IDX_CFE0A4E944F5D008 ON clothing_item (brand_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE clothing_items_categories DROP FOREIGN KEY FK_DE3AA3CCAA13B545');
        $this->addSql('ALTER TABLE clothing_items_categories DROP FOREIGN KEY FK_DE3AA3CC12469DE2');
        $this->addSql('DROP TABLE clothing_items_categories');
        $this->addSql('ALTER TABLE clothing_item DROP FOREIGN KEY FK_CFE0A4E944F5D008');
        $this->addSql('DROP INDEX IDX_CFE0A4E944F5D008 ON clothing_item');
        $this->addSql('ALTER TABLE clothing_item ADD category_id INT NOT NULL');
    }
}
