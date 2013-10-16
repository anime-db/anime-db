<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use AnimeDb\Bundle\CatalogBundle\Entity\Task;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131016102831_AddAppUpdateTask extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // run a the application update every monday at 2 am
        $monday = (86400 * (8 - (date('w') ?: 7)));
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
                    "animedb:update",
                    "'.date('Y-m-d 02:00:00', time()+$monday).'",
                    "+7 day",
                    '.Task::STATUS_ENABLED.'
                )');
    }

    public function down(Schema $schema)
    {
        $this->addSql('
            DELETE FROM
                "task"
            WHERE
                "command" = "animedb:update"
        ');
    }
}