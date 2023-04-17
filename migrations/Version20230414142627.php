<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230414142627 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE faq CHANGE questions questions TINYTEXT NOT NULL COLLATE `utf8mb4_general_ci`, CHANGE reponses reponses TINYTEXT NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE produit ADD description LONGTEXT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE faq CHANGE questions questions VARCHAR(255) NOT NULL COLLATE `utf8mb4_general_ci`, CHANGE reponses reponses VARCHAR(255) NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE produit DROP description');
    }
}
