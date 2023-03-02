<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230301154619 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE produto (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, unit SMALLINT NOT NULL, weight DOUBLE PRECISION NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Produtofornecedor (Produto_id INT NOT NULL, Fornecedor_id INT NOT NULL, INDEX IDX_E87E01365F01FE86 (Produto_id), INDEX IDX_E87E01366095FD68 (Fornecedor_id), PRIMARY KEY(Produto_id, Fornecedor_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Produtocategoria (Produto_id INT NOT NULL, Categoria_id INT NOT NULL, INDEX IDX_E4DCEDF05F01FE86 (Produto_id), INDEX IDX_E4DCEDF02BA6AB82 (Categoria_id), PRIMARY KEY(Produto_id, Categoria_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produto_has_estoque (id INT AUTO_INCREMENT NOT NULL, produto_id INT NOT NULL, estoque_id INT NOT NULL, quantity INT NOT NULL, qtt_max INT NOT NULL, qtt_min INT NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, INDEX IDX_78F011B105CFD56 (produto_id), INDEX IDX_78F011B2C3A51FC (estoque_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Produtofornecedor ADD CONSTRAINT FK_E87E01365F01FE86 FOREIGN KEY (Produto_id) REFERENCES produto (id)');
        $this->addSql('ALTER TABLE Produtofornecedor ADD CONSTRAINT FK_E87E01366095FD68 FOREIGN KEY (Fornecedor_id) REFERENCES fornecedor (id)');
        $this->addSql('ALTER TABLE Produtocategoria ADD CONSTRAINT FK_E4DCEDF05F01FE86 FOREIGN KEY (Produto_id) REFERENCES produto (id)');
        $this->addSql('ALTER TABLE Produtocategoria ADD CONSTRAINT FK_E4DCEDF02BA6AB82 FOREIGN KEY (Categoria_id) REFERENCES categoria (id)');
        $this->addSql('ALTER TABLE produto_has_estoque ADD CONSTRAINT FK_78F011B105CFD56 FOREIGN KEY (produto_id) REFERENCES produto (id)');
        $this->addSql('ALTER TABLE produto_has_estoque ADD CONSTRAINT FK_78F011B2C3A51FC FOREIGN KEY (estoque_id) REFERENCES estoque (id)');
        $this->addSql('ALTER TABLE categoria CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE contato DROP FOREIGN KEY FK_C384AB42A00FBD41');
        $this->addSql('DROP INDEX IDX_C384AB42A00FBD41 ON contato');
        $this->addSql('ALTER TABLE contato ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP, ADD updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, DROP forncededor_id_id');
        $this->addSql('ALTER TABLE estoque CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE fornecedor CHANGE responsavel responsavel TIME DEFAULT NULL COMMENT \'(DC2Type:time_immutable)\', CHANGE status status TINYINT(1) DEFAULT 1 NOT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE updated_at updated_at DATETIME DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE fornecedorcontato ADD CONSTRAINT FK_EB729D096095FD68 FOREIGN KEY (Fornecedor_id) REFERENCES fornecedor (id)');
        $this->addSql('ALTER TABLE fornecedorcontato ADD CONSTRAINT FK_EB729D09FD24BD96 FOREIGN KEY (Contato_id) REFERENCES contato (id)');
        $this->addSql('ALTER TABLE user CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE updated_at updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Produtofornecedor DROP FOREIGN KEY FK_E87E01365F01FE86');
        $this->addSql('ALTER TABLE Produtofornecedor DROP FOREIGN KEY FK_E87E01366095FD68');
        $this->addSql('ALTER TABLE Produtocategoria DROP FOREIGN KEY FK_E4DCEDF05F01FE86');
        $this->addSql('ALTER TABLE Produtocategoria DROP FOREIGN KEY FK_E4DCEDF02BA6AB82');
        $this->addSql('ALTER TABLE produto_has_estoque DROP FOREIGN KEY FK_78F011B105CFD56');
        $this->addSql('ALTER TABLE produto_has_estoque DROP FOREIGN KEY FK_78F011B2C3A51FC');
        $this->addSql('DROP TABLE produto');
        $this->addSql('DROP TABLE Produtofornecedor');
        $this->addSql('DROP TABLE Produtocategoria');
        $this->addSql('DROP TABLE produto_has_estoque');
        $this->addSql('ALTER TABLE categoria CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE contato ADD forncededor_id_id INT NOT NULL, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE contato ADD CONSTRAINT FK_C384AB42A00FBD41 FOREIGN KEY (forncededor_id_id) REFERENCES fornecedor (id)');
        $this->addSql('CREATE INDEX IDX_C384AB42A00FBD41 ON contato (forncededor_id_id)');
        $this->addSql('ALTER TABLE estoque CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE fornecedor CHANGE responsavel responsavel VARCHAR(124) DEFAULT NULL, CHANGE status status TINYINT(1) NOT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT \'(DC2Type:datetime_immutable)\', CHANGE updated_at updated_at DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE Fornecedorcontato DROP FOREIGN KEY FK_EB729D096095FD68');
        $this->addSql('ALTER TABLE Fornecedorcontato DROP FOREIGN KEY FK_EB729D09FD24BD96');
        $this->addSql('ALTER TABLE user CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE updated_at updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}
