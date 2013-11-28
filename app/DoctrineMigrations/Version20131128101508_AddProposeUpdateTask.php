<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use AnimeDb\Bundle\AppBundle\Entity\Task;
use AnimeDb\Bundle\AppBundle\Command\ProposeUpdateCommand;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131128101508_AddProposeUpdateTask extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // run a propose update at 1 am
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
                    "animedb:propose-update",
                    "'.date('Y-m-d 01:00:00', time()+ProposeUpdateCommand::INERVAL_UPDATE).'",
                    "+'.ProposeUpdateCommand::INERVAL_UPDATE.' second",
                    '.Task::STATUS_ENABLED.'
                )');
    }

    public function down(Schema $schema)
    {
        $this->addSql('
            DELETE FROM
                "task"
            WHERE
                "command" = "animedb:propose-update"
        ');
    }
}