<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20250218144839 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout d un tableau pour les elements detecter par l ia';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE detected_clothing (id INT AUTO_INCREMENT NOT NULL, image_path VARCHAR(255) NOT NULL, detected_items JSON NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE detected_clothing');
    }
}
