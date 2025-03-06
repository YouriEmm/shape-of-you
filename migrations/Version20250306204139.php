<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250306204139 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ainotification (id INT AUTO_INCREMENT NOT NULL, message LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', status VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE clothing_item (id INT AUTO_INCREMENT NOT NULL, brand_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, category VARCHAR(255) NOT NULL, INDEX IDX_CFE0A4E944F5D008 (brand_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, outfit_id INT NOT NULL, owner_id INT NOT NULL, parent_id INT DEFAULT NULL, content LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_9474526CAE96E385 (outfit_id), INDEX IDX_9474526C7E3C61F9 (owner_id), INDEX IDX_9474526C727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE detected_clothing (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, image_path VARCHAR(255) NOT NULL, detected_items JSON NOT NULL, INDEX IDX_B8EB74727E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE history_entry (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, outfit_id INT DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_729995177E3C61F9 (owner_id), INDEX IDX_72999517AE96E385 (outfit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `like` (id INT AUTO_INCREMENT NOT NULL, outfit_id INT NOT NULL, owner_id INT DEFAULT NULL, INDEX IDX_AC6340B3AE96E385 (outfit_id), INDEX IDX_AC6340B37E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE outfit (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, public TINYINT(1) NOT NULL, INDEX IDX_320296017E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE outfit_clothing_item (outfit_id INT NOT NULL, clothing_item_id INT NOT NULL, INDEX IDX_D50A35E3AE96E385 (outfit_id), INDEX IDX_D50A35E3AA13B545 (clothing_item_id), PRIMARY KEY(outfit_id, clothing_item_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partner (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, website VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wardrobe (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, UNIQUE INDEX UNIQ_2C80050E7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wardrobe_clothing_item (wardrobe_id INT NOT NULL, clothing_item_id INT NOT NULL, INDEX IDX_5A4570A4FC109F73 (wardrobe_id), INDEX IDX_5A4570A4AA13B545 (clothing_item_id), PRIMARY KEY(wardrobe_id, clothing_item_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE clothing_item ADD CONSTRAINT FK_CFE0A4E944F5D008 FOREIGN KEY (brand_id) REFERENCES partner (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CAE96E385 FOREIGN KEY (outfit_id) REFERENCES outfit (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C727ACA70 FOREIGN KEY (parent_id) REFERENCES comment (id)');
        $this->addSql('ALTER TABLE detected_clothing ADD CONSTRAINT FK_B8EB74727E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE history_entry ADD CONSTRAINT FK_729995177E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE history_entry ADD CONSTRAINT FK_72999517AE96E385 FOREIGN KEY (outfit_id) REFERENCES outfit (id)');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B3AE96E385 FOREIGN KEY (outfit_id) REFERENCES outfit (id)');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B37E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE outfit ADD CONSTRAINT FK_320296017E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE outfit_clothing_item ADD CONSTRAINT FK_D50A35E3AE96E385 FOREIGN KEY (outfit_id) REFERENCES outfit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE outfit_clothing_item ADD CONSTRAINT FK_D50A35E3AA13B545 FOREIGN KEY (clothing_item_id) REFERENCES clothing_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE wardrobe ADD CONSTRAINT FK_2C80050E7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE wardrobe_clothing_item ADD CONSTRAINT FK_5A4570A4FC109F73 FOREIGN KEY (wardrobe_id) REFERENCES wardrobe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE wardrobe_clothing_item ADD CONSTRAINT FK_5A4570A4AA13B545 FOREIGN KEY (clothing_item_id) REFERENCES clothing_item (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE clothing_item DROP FOREIGN KEY FK_CFE0A4E944F5D008');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CAE96E385');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C7E3C61F9');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C727ACA70');
        $this->addSql('ALTER TABLE detected_clothing DROP FOREIGN KEY FK_B8EB74727E3C61F9');
        $this->addSql('ALTER TABLE history_entry DROP FOREIGN KEY FK_729995177E3C61F9');
        $this->addSql('ALTER TABLE history_entry DROP FOREIGN KEY FK_72999517AE96E385');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B3AE96E385');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B37E3C61F9');
        $this->addSql('ALTER TABLE outfit DROP FOREIGN KEY FK_320296017E3C61F9');
        $this->addSql('ALTER TABLE outfit_clothing_item DROP FOREIGN KEY FK_D50A35E3AE96E385');
        $this->addSql('ALTER TABLE outfit_clothing_item DROP FOREIGN KEY FK_D50A35E3AA13B545');
        $this->addSql('ALTER TABLE wardrobe DROP FOREIGN KEY FK_2C80050E7E3C61F9');
        $this->addSql('ALTER TABLE wardrobe_clothing_item DROP FOREIGN KEY FK_5A4570A4FC109F73');
        $this->addSql('ALTER TABLE wardrobe_clothing_item DROP FOREIGN KEY FK_5A4570A4AA13B545');
        $this->addSql('DROP TABLE ainotification');
        $this->addSql('DROP TABLE clothing_item');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE detected_clothing');
        $this->addSql('DROP TABLE history_entry');
        $this->addSql('DROP TABLE `like`');
        $this->addSql('DROP TABLE outfit');
        $this->addSql('DROP TABLE outfit_clothing_item');
        $this->addSql('DROP TABLE partner');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE wardrobe');
        $this->addSql('DROP TABLE wardrobe_clothing_item');
    }
}
