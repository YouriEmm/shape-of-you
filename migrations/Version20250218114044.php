<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20250218114044 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Création des Entity dans la BDD';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE ainotification (id INT AUTO_INCREMENT NOT NULL, message LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', status VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE clothing_item (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE clothing_items_categories (clothing_item_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_DE3AA3CCAA13B545 (clothing_item_id), INDEX IDX_DE3AA3CC12469DE2 (category_id), PRIMARY KEY(clothing_item_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, publication_id INT NOT NULL, owner_id INT NOT NULL, content LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_9474526C38B217A7 (publication_id), INDEX IDX_9474526C7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE history_entry (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, outfit_id INT DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_729995177E3C61F9 (owner_id), INDEX IDX_72999517AE96E385 (outfit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `like` (id INT AUTO_INCREMENT NOT NULL, publication_id INT NOT NULL, owner_id INT DEFAULT NULL, INDEX IDX_AC6340B338B217A7 (publication_id), INDEX IDX_AC6340B37E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE outfit (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_320296017E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE outfit_clothing_item (outfit_id INT NOT NULL, clothing_item_id INT NOT NULL, INDEX IDX_D50A35E3AE96E385 (outfit_id), INDEX IDX_D50A35E3AA13B545 (clothing_item_id), PRIMARY KEY(outfit_id, clothing_item_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partner (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, website VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE publication (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, outfit_id INT NOT NULL, description VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_AF3C67797E3C61F9 (owner_id), INDEX IDX_AF3C6779AE96E385 (outfit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wardrobe (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, UNIQUE INDEX UNIQ_2C80050E7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wardrobe_clothing_item (wardrobe_id INT NOT NULL, clothing_item_id INT NOT NULL, INDEX IDX_5A4570A4FC109F73 (wardrobe_id), INDEX IDX_5A4570A4AA13B545 (clothing_item_id), PRIMARY KEY(wardrobe_id, clothing_item_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE clothing_items_categories ADD CONSTRAINT FK_DE3AA3CCAA13B545 FOREIGN KEY (clothing_item_id) REFERENCES clothing_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE clothing_items_categories ADD CONSTRAINT FK_DE3AA3CC12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C38B217A7 FOREIGN KEY (publication_id) REFERENCES publication (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE history_entry ADD CONSTRAINT FK_729995177E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE history_entry ADD CONSTRAINT FK_72999517AE96E385 FOREIGN KEY (outfit_id) REFERENCES outfit (id)');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B338B217A7 FOREIGN KEY (publication_id) REFERENCES publication (id)');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B37E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE outfit ADD CONSTRAINT FK_320296017E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE outfit_clothing_item ADD CONSTRAINT FK_D50A35E3AE96E385 FOREIGN KEY (outfit_id) REFERENCES outfit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE outfit_clothing_item ADD CONSTRAINT FK_D50A35E3AA13B545 FOREIGN KEY (clothing_item_id) REFERENCES clothing_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE publication ADD CONSTRAINT FK_AF3C67797E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE publication ADD CONSTRAINT FK_AF3C6779AE96E385 FOREIGN KEY (outfit_id) REFERENCES outfit (id)');
        $this->addSql('ALTER TABLE wardrobe ADD CONSTRAINT FK_2C80050E7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE wardrobe_clothing_item ADD CONSTRAINT FK_5A4570A4FC109F73 FOREIGN KEY (wardrobe_id) REFERENCES wardrobe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE wardrobe_clothing_item ADD CONSTRAINT FK_5A4570A4AA13B545 FOREIGN KEY (clothing_item_id) REFERENCES clothing_item (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE clothing_items_categories DROP FOREIGN KEY FK_DE3AA3CCAA13B545');
        $this->addSql('ALTER TABLE clothing_items_categories DROP FOREIGN KEY FK_DE3AA3CC12469DE2');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C38B217A7');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C7E3C61F9');
        $this->addSql('ALTER TABLE history_entry DROP FOREIGN KEY FK_729995177E3C61F9');
        $this->addSql('ALTER TABLE history_entry DROP FOREIGN KEY FK_72999517AE96E385');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B338B217A7');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B37E3C61F9');
        $this->addSql('ALTER TABLE outfit DROP FOREIGN KEY FK_320296017E3C61F9');
        $this->addSql('ALTER TABLE outfit_clothing_item DROP FOREIGN KEY FK_D50A35E3AE96E385');
        $this->addSql('ALTER TABLE outfit_clothing_item DROP FOREIGN KEY FK_D50A35E3AA13B545');
        $this->addSql('ALTER TABLE publication DROP FOREIGN KEY FK_AF3C67797E3C61F9');
        $this->addSql('ALTER TABLE publication DROP FOREIGN KEY FK_AF3C6779AE96E385');
        $this->addSql('ALTER TABLE wardrobe DROP FOREIGN KEY FK_2C80050E7E3C61F9');
        $this->addSql('ALTER TABLE wardrobe_clothing_item DROP FOREIGN KEY FK_5A4570A4FC109F73');
        $this->addSql('ALTER TABLE wardrobe_clothing_item DROP FOREIGN KEY FK_5A4570A4AA13B545');
        $this->addSql('DROP TABLE ainotification');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE clothing_item');
        $this->addSql('DROP TABLE clothing_items_categories');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE history_entry');
        $this->addSql('DROP TABLE `like`');
        $this->addSql('DROP TABLE outfit');
        $this->addSql('DROP TABLE outfit_clothing_item');
        $this->addSql('DROP TABLE partner');
        $this->addSql('DROP TABLE publication');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE wardrobe');
        $this->addSql('DROP TABLE wardrobe_clothing_item');
    }
}
