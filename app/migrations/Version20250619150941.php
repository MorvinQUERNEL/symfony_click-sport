<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250619150941 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE orders_products (id INT AUTO_INCREMENT NOT NULL, orders_id INT DEFAULT NULL, rating INT DEFAULT NULL, quantity INT NOT NULL, price DOUBLE PRECISION NOT NULL, rating_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_749C879CCFFE9AD6 (orders_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE pictures (id INT AUTO_INCREMENT NOT NULL, path VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE products (id INT AUTO_INCREMENT NOT NULL, orders_products_id INT DEFAULT NULL, title VARCHAR(50) NOT NULL, price DOUBLE PRECISION NOT NULL, description VARCHAR(255) DEFAULT NULL, notice VARCHAR(255) DEFAULT NULL, INDEX IDX_B3BA5A5A1F335533 (orders_products_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE users_products (users_id INT NOT NULL, products_id INT NOT NULL, INDEX IDX_C8FB718067B3B43D (users_id), INDEX IDX_C8FB71806C8A81A9 (products_id), PRIMARY KEY(users_id, products_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE orders_products ADD CONSTRAINT FK_749C879CCFFE9AD6 FOREIGN KEY (orders_id) REFERENCES orders (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A1F335533 FOREIGN KEY (orders_products_id) REFERENCES orders_products (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users_products ADD CONSTRAINT FK_C8FB718067B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users_products ADD CONSTRAINT FK_C8FB71806C8A81A9 FOREIGN KEY (products_id) REFERENCES products (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE addresses ADD users_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE addresses ADD CONSTRAINT FK_6FCA751667B3B43D FOREIGN KEY (users_id) REFERENCES users (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6FCA751667B3B43D ON addresses (users_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE orders ADD users_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE67B3B43D FOREIGN KEY (users_id) REFERENCES users (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E52FFDEE67B3B43D ON orders (users_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE orders_products DROP FOREIGN KEY FK_749C879CCFFE9AD6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5A1F335533
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users_products DROP FOREIGN KEY FK_C8FB718067B3B43D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users_products DROP FOREIGN KEY FK_C8FB71806C8A81A9
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE categories
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE orders_products
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE pictures
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE products
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE users_products
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE addresses DROP FOREIGN KEY FK_6FCA751667B3B43D
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_6FCA751667B3B43D ON addresses
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE addresses DROP users_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE67B3B43D
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_E52FFDEE67B3B43D ON orders
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE orders DROP users_id
        SQL);
    }
}
