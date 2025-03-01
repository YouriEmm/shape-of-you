<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20250301171021 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout des outfit public ou non pour que tout le monde les regardes';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE outfit ADD public TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE outfit DROP public');
    }
}
