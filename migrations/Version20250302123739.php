<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20250302123739 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout de la relation entre les vêtements detecter et un user pour gerer son historique de vêtement detecter';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE detected_clothing ADD owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE detected_clothing ADD CONSTRAINT FK_B8EB74727E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_B8EB74727E3C61F9 ON detected_clothing (owner_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE detected_clothing DROP FOREIGN KEY FK_B8EB74727E3C61F9');
        $this->addSql('DROP INDEX IDX_B8EB74727E3C61F9 ON detected_clothing');
        $this->addSql('ALTER TABLE detected_clothing DROP owner_id');
    }
}
