<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250402142929 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE wish (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, user_id INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, is_published TINYINT(1) NOT NULL, date_created DATETIME NOT NULL, date_updated DATETIME DEFAULT NULL, INDEX IDX_D7D174C912469DE2 (category_id), INDEX IDX_D7D174C9A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE wish ADD CONSTRAINT FK_D7D174C912469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE wish ADD CONSTRAINT FK_D7D174C9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE wish DROP FOREIGN KEY FK_D7D174C912469DE2');
        $this->addSql('ALTER TABLE wish DROP FOREIGN KEY FK_D7D174C9A76ED395');
        $this->addSql('DROP TABLE wish');
    }
}
