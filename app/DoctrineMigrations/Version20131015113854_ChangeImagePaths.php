<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131015113854_ChangeImagePaths extends AbstractMigration
{
    public function up(Schema $schema)
    {
        /**
         * Migration is not critical
         * Old format for storing image files to the new format
         *
         * Old format(date added image):
         *    media/{Y}/{m}/
         *
         * New Format(date added item):
         *    media/{Y}/{m}/{d}/{His}/
         */
    }

    public function down(Schema $schema)
    {
        // the down migration is not need
    }
}