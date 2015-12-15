<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151215195940 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE tpe_ambito (id VARCHAR(255) NOT NULL, nombre VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX ambito_id_idx ON tpe_ambito (id)');
        $this->addSql('CREATE TABLE tpe_politica (id VARCHAR(255) NOT NULL, ambito VARCHAR(255) DEFAULT NULL, partido VARCHAR(255) DEFAULT NULL, fuentes JSON NOT NULL, contenido TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D51AB6CB53BFC1BC ON tpe_politica (ambito)');
        $this->addSql('CREATE INDEX IDX_D51AB6CB4E79750B ON tpe_politica (partido)');
        $this->addSql('CREATE INDEX politica_id_idx ON tpe_politica (id)');
        $this->addSql('CREATE TABLE tpe_partido (id VARCHAR(255) NOT NULL, nombre VARCHAR(255) NOT NULL, siglas VARCHAR(255) NOT NULL, programa VARCHAR(512) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX partido_id_idx ON tpe_partido (id)');
        $this->addSql('CREATE TABLE tpe_mi_programa (id UUID NOT NULL, politicas JSON NOT NULL, terminado BOOLEAN NOT NULL, publico BOOLEAN NOT NULL, ultimaModificacion TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX mi_programa_id_idx ON tpe_mi_programa (id)');
        $this->addSql('ALTER TABLE tpe_politica ADD CONSTRAINT FK_D51AB6CB53BFC1BC FOREIGN KEY (ambito) REFERENCES tpe_ambito (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tpe_politica ADD CONSTRAINT FK_D51AB6CB4E79750B FOREIGN KEY (partido) REFERENCES tpe_partido (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE tpe_politica DROP CONSTRAINT FK_D51AB6CB53BFC1BC');
        $this->addSql('ALTER TABLE tpe_politica DROP CONSTRAINT FK_D51AB6CB4E79750B');
        $this->addSql('DROP TABLE tpe_ambito');
        $this->addSql('DROP TABLE tpe_politica');
        $this->addSql('DROP TABLE tpe_partido');
        $this->addSql('DROP TABLE tpe_mi_programa');
    }
}
