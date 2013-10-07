<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use AnimeDb\Bundle\CatalogBundle\Entity\Task;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131007112432_AddScanStoragesTask extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // run a scan of the storages every day at 1 am
        $this->addSql('
            INSERT INTO
                "task"
                (
                    "command",
                    "next_run",
                    "modify",
                    "status"
                )
            VALUES
                (
                    "animedb:scan-storage",
                    "'.date('Y-m-d 01:00:00', time()+86400).'",
                    "+1 day",
                    '.Task::STATUS_ENABLED.'
                )');
    }

    public function down(Schema $schema)
    {
        $this->addSql('
            DELETE FROM
                "task"
            WHERE
                "command" = "animedb:scan-storage"
        ');
    }
}
