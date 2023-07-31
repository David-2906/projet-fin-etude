<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230727205656 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DA76ED395');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF2A76ED395');
        $this->addSql('ALTER TABLE panier_produit DROP FOREIGN KEY FK_D31F28A6F77D927C');
        $this->addSql('ALTER TABLE panier_produit DROP FOREIGN KEY FK_D31F28A6F347EFB');
        $this->addSql('ALTER TABLE produit_commande DROP FOREIGN KEY FK_47F5946E82EA2E54');
        $this->addSql('ALTER TABLE produit_commande DROP FOREIGN KEY FK_47F5946EF347EFB');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE panier');
        $this->addSql('DROP TABLE panier_produit');
        $this->addSql('DROP TABLE produit_commande');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, numero_commande INT NOT NULL, date_commande DATETIME NOT NULL, montant DOUBLE PRECISION NOT NULL, paye TINYINT(1) NOT NULL, INDEX IDX_6EEAA67DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE panier (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_24CC0DF2A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE panier_produit (id INT AUTO_INCREMENT NOT NULL, produit_id INT NOT NULL, panier_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_D31F28A6F347EFB (produit_id), INDEX IDX_D31F28A6F77D927C (panier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE produit_commande (produit_id INT NOT NULL, commande_id INT NOT NULL, INDEX IDX_47F5946EF347EFB (produit_id), INDEX IDX_47F5946E82EA2E54 (commande_id), PRIMARY KEY(produit_id, commande_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE panier_produit ADD CONSTRAINT FK_D31F28A6F77D927C FOREIGN KEY (panier_id) REFERENCES panier (id)');
        $this->addSql('ALTER TABLE panier_produit ADD CONSTRAINT FK_D31F28A6F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE produit_commande ADD CONSTRAINT FK_47F5946E82EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE produit_commande ADD CONSTRAINT FK_47F5946EF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE');
    }
}
