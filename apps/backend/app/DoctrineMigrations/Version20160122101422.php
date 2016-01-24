<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160122101422 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE tpe_scope (id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX scope_id_idx ON tpe_scope (id)');
        $this->addSql('CREATE TABLE tpe_policy (id VARCHAR(255) NOT NULL, scope VARCHAR(255) DEFAULT NULL, party VARCHAR(255) DEFAULT NULL, sources JSON NOT NULL, content TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E0A21D75AF55D3 ON tpe_policy (scope)');
        $this->addSql('CREATE INDEX IDX_E0A21D7589954EE0 ON tpe_policy (party)');
        $this->addSql('CREATE INDEX policy_id_idx ON tpe_policy (id)');
        $this->addSql('CREATE TABLE tpe_party (id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, acronym VARCHAR(255) NOT NULL, programmeUrl VARCHAR(512) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX party_id_idx ON tpe_party (id)');
        $this->addSql('CREATE TABLE tpe_my_programme (id UUID NOT NULL, policies JSON NOT NULL, completed BOOLEAN NOT NULL, public BOOLEAN NOT NULL, lastModification TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX my_programme_id_idx ON tpe_my_programme (id)');
        $this->addSql('ALTER TABLE tpe_policy ADD CONSTRAINT FK_E0A21D75AF55D3 FOREIGN KEY (scope) REFERENCES tpe_scope (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tpe_policy ADD CONSTRAINT FK_E0A21D7589954EE0 FOREIGN KEY (party) REFERENCES tpe_party (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE tpe_policy DROP CONSTRAINT FK_E0A21D75AF55D3');
        $this->addSql('ALTER TABLE tpe_policy DROP CONSTRAINT FK_E0A21D7589954EE0');
        $this->addSql('DROP TABLE tpe_scope');
        $this->addSql('DROP TABLE tpe_policy');
        $this->addSql('DROP TABLE tpe_party');
        $this->addSql('DROP TABLE tpe_my_programme');
    }
}
