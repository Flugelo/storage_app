<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230302002926 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE estoque ADD qtt_max DOUBLE PRECISION DEFAULT NULL, ADD qtt_min DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE produto CHANGE unit unit SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE produto_has_estoque DROP qtt_max, DROP qtt_min');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE estoque DROP qtt_max, DROP qtt_min');
        $this->addSql('ALTER TABLE produto CHANGE unit unit SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE produto_has_estoque ADD qtt_max INT NOT NULL, ADD qtt_min INT NOT NULL');
    }
}
