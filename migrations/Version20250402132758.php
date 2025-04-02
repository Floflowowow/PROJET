<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250402132758 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__cart AS SELECT id, users_id, quantite FROM cart
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE cart
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE cart (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, quantite INTEGER DEFAULT NULL, CONSTRAINT FK_BA388B7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO cart (id, user_id, quantite) SELECT id, users_id, quantite FROM __temp__cart
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__cart
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_BA388B7A76ED395 ON cart (user_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__cart AS SELECT id, user_id, quantite FROM cart
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE cart
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE cart (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, users_id INTEGER NOT NULL, quantite INTEGER DEFAULT NULL, CONSTRAINT FK_BA388B767B3B43D FOREIGN KEY (users_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO cart (id, users_id, quantite) SELECT id, user_id, quantite FROM __temp__cart
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__cart
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_BA388B767B3B43D ON cart (users_id)
        SQL);
    }
}
