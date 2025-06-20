<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250620100312 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE pictures ADD products_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE pictures ADD CONSTRAINT FK_8F7C2FC06C8A81A9 FOREIGN KEY (products_id) REFERENCES products (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_8F7C2FC06C8A81A9 ON pictures (products_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE products ADD categories_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5AA21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B3BA5A5AA21214B7 ON products (categories_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5AA21214B7
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_B3BA5A5AA21214B7 ON products
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE products DROP categories_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE pictures DROP FOREIGN KEY FK_8F7C2FC06C8A81A9
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_8F7C2FC06C8A81A9 ON pictures
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE pictures DROP products_id
        SQL);
    }
}
