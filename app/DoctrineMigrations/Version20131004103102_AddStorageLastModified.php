<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131004103102_AddStorageLastModified extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // create temp table from new structure
        $this->addSql('CREATE TABLE "_new" (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            name VARCHAR(128) NOT NULL,
            description TEXT NOT NULL,
            type VARCHAR(16) NOT NULL,
            path TEXT DEFAULT NULL,
            modified DATE DEFAULT NULL
        )');

        $this->addSql('
            INSERT INTO
                "_new"
            SELECT
                id, name, description, type, path, NULL
            FROM
                "storage"
        ');
        // rename new to origin and drop origin
        $this->addSql('ALTER TABLE storage RENAME TO _origin');
        $this->addSql('ALTER TABLE _new RENAME TO storage');
        $this->addSql('DROP TABLE _origin');

        $this->addSql('CREATE INDEX storage_type_idx ON storage (type)');
    }

    public function down(Schema $schema)
    {
        // create temp table from origin structure
        $this->addSql('CREATE TABLE "_new" (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            name VARCHAR(128) NOT NULL,
            description TEXT NOT NULL,
            type VARCHAR(16) NOT NULL,
            path TEXT DEFAULT NULL
        )');
        $this->addSql('
            INSERT INTO
                "_new"
            SELECT
                id, name, description, type, path
            FROM
                "storage"
        ');
        // rename new to origin and drop origin
        $this->addSql('ALTER TABLE storage RENAME TO _origin');
        $this->addSql('ALTER TABLE _new RENAME TO storage');
        $this->addSql('DROP TABLE _origin');

        $this->addSql('DROP INDEX storage_type_idx');
    }
}