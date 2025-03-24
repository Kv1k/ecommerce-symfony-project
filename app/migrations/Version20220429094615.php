<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220429094615 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE quantity (id INT AUTO_INCREMENT NOT NULL, cart_id INT DEFAULT NULL, quantity INT NOT NULL, INDEX IDX_9FF316361AD5CDBF (cart_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quantity_product (quantity_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_20FECDA87E8B4AFC (quantity_id), INDEX IDX_20FECDA84584665A (product_id), PRIMARY KEY(quantity_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE quantity ADD CONSTRAINT FK_9FF316361AD5CDBF FOREIGN KEY (cart_id) REFERENCES card (id)');
        $this->addSql('ALTER TABLE quantity_product ADD CONSTRAINT FK_20FECDA87E8B4AFC FOREIGN KEY (quantity_id) REFERENCES quantity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE quantity_product ADD CONSTRAINT FK_20FECDA84584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE card_product DROP FOREIGN KEY FK_84508EDB4ACC9A20');
        $this->addSql('ALTER TABLE card_product ADD CONSTRAINT FK_84508EDB4ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quantity_product DROP FOREIGN KEY FK_20FECDA87E8B4AFC');
        $this->addSql('DROP TABLE quantity');
        $this->addSql('DROP TABLE quantity_product');
        $this->addSql('ALTER TABLE card_product DROP FOREIGN KEY FK_84508EDB4ACC9A20');
        $this->addSql('ALTER TABLE card_product ADD CONSTRAINT FK_84508EDB4ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
