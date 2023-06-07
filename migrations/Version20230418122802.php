<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230418122802 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit ADD cepage_id INT NOT NULL');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC278AC6BB8A FOREIGN KEY (cepage_id) REFERENCES cepage (id)');
        $this->addSql('CREATE INDEX IDX_29A5EC278AC6BB8A ON produit (cepage_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC278AC6BB8A');
        $this->addSql('DROP INDEX IDX_29A5EC278AC6BB8A ON produit');
        $this->addSql('ALTER TABLE produit DROP cepage_id');
    }
}
