<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20250304233337 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Changement direct sur outfit et non publication';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C38B217A7');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B338B217A7');
        $this->addSql('ALTER TABLE publication DROP FOREIGN KEY FK_AF3C67797E3C61F9');
        $this->addSql('ALTER TABLE publication DROP FOREIGN KEY FK_AF3C6779AE96E385');
        $this->addSql('DROP TABLE publication');
        $this->addSql('DROP INDEX IDX_9474526C38B217A7 ON comment');
        $this->addSql('ALTER TABLE comment CHANGE publication_id outfit_id INT NOT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CAE96E385 FOREIGN KEY (outfit_id) REFERENCES outfit (id)');
        $this->addSql('CREATE INDEX IDX_9474526CAE96E385 ON comment (outfit_id)');
        $this->addSql('DROP INDEX IDX_AC6340B338B217A7 ON `like`');
        $this->addSql('ALTER TABLE `like` CHANGE publication_id outfit_id INT NOT NULL');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B3AE96E385 FOREIGN KEY (outfit_id) REFERENCES outfit (id)');
        $this->addSql('CREATE INDEX IDX_AC6340B3AE96E385 ON `like` (outfit_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TABLE publication (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, outfit_id INT NOT NULL, description VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, INDEX IDX_AF3C67797E3C61F9 (owner_id), INDEX IDX_AF3C6779AE96E385 (outfit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE publication ADD CONSTRAINT FK_AF3C67797E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE publication ADD CONSTRAINT FK_AF3C6779AE96E385 FOREIGN KEY (outfit_id) REFERENCES outfit (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CAE96E385');
        $this->addSql('DROP INDEX IDX_9474526CAE96E385 ON comment');
        $this->addSql('ALTER TABLE comment CHANGE outfit_id publication_id INT NOT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C38B217A7 FOREIGN KEY (publication_id) REFERENCES publication (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_9474526C38B217A7 ON comment (publication_id)');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B3AE96E385');
        $this->addSql('DROP INDEX IDX_AC6340B3AE96E385 ON `like`');
        $this->addSql('ALTER TABLE `like` CHANGE outfit_id publication_id INT NOT NULL');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B338B217A7 FOREIGN KEY (publication_id) REFERENCES publication (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_AC6340B338B217A7 ON `like` (publication_id)');
    }
}
