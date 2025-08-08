<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250808204229 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE connexion (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, cree_par_id INT DEFAULT NULL, modifie_par_id INT DEFAULT NULL, user_agent VARCHAR(255) DEFAULT NULL, date_connexion DATE NOT NULL, date_creation DATETIME DEFAULT NULL, date_modification DATETIME DEFAULT NULL, INDEX IDX_936BF99CA76ED395 (user_id), INDEX IDX_936BF99CFC29C013 (cree_par_id), INDEX IDX_936BF99C553B2554 (modifie_par_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE connexion ADD CONSTRAINT FK_936BF99CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE connexion ADD CONSTRAINT FK_936BF99CFC29C013 FOREIGN KEY (cree_par_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE connexion ADD CONSTRAINT FK_936BF99C553B2554 FOREIGN KEY (modifie_par_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD path VARCHAR(255) DEFAULT NULL, ADD filename VARCHAR(255) DEFAULT NULL, CHANGE image_file_name imageFilename VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE connexion DROP FOREIGN KEY FK_936BF99CA76ED395');
        $this->addSql('ALTER TABLE connexion DROP FOREIGN KEY FK_936BF99CFC29C013');
        $this->addSql('ALTER TABLE connexion DROP FOREIGN KEY FK_936BF99C553B2554');
        $this->addSql('DROP TABLE connexion');
        $this->addSql('ALTER TABLE user ADD image_file_name VARCHAR(255) DEFAULT NULL, DROP imageFilename, DROP path, DROP filename');
    }
}
