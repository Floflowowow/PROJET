<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250402132322 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE user_cart
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__cart AS SELECT id, quantite FROM cart
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE cart
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE cart (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, users_id INTEGER NOT NULL, quantite INTEGER DEFAULT NULL, CONSTRAINT FK_BA388B767B3B43D FOREIGN KEY (users_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO cart (id, quantite) SELECT id, quantite FROM __temp__cart
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__cart
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_BA388B767B3B43D ON cart (users_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE user_cart (user_id INTEGER NOT NULL, cart_id INTEGER NOT NULL, PRIMARY KEY(user_id, cart_id), CONSTRAINT FK_7122C47EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_7122C47E1AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_7122C47E1AD5CDBF ON user_cart (cart_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_7122C47EA76ED395 ON user_cart (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__cart AS SELECT id, quantite FROM cart
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE cart
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE cart (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, quantite INTEGER DEFAULT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO cart (id, quantite) SELECT id, quantite FROM __temp__cart
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__cart
        SQL);
    }
}
