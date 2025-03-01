<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20250225132347 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Modification du role du USER';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD roles JSON NOT NULL, DROP role');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD role VARCHAR(255) NOT NULL, DROP roles');
    }
}
