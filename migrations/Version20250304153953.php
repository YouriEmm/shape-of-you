<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20250304153953 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Brand can be null';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE clothing_item DROP FOREIGN KEY FK_CFE0A4E944F5D008');
        $this->addSql('ALTER TABLE clothing_item ADD CONSTRAINT FK_CFE0A4E944F5D008 FOREIGN KEY (brand_id) REFERENCES partner (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE clothing_item DROP FOREIGN KEY FK_CFE0A4E944F5D008');
        $this->addSql('ALTER TABLE clothing_item ADD CONSTRAINT FK_CFE0A4E944F5D008 FOREIGN KEY (brand_id) REFERENCES partner (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
