<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\HttpFoundation\File\File;

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
        $media = __DIR__.'/../../web/media/';

        // move covers
        $items = $this->connection->fetchAll('
            SELECT
                `id`,
                `cover`,
                `date_add`
            FROM
                `item`
            WHERE
                `cover` IS NOT NULL AND
                `cover` != "" AND
                `cover`  NOT LIKE "example/%"'
        );
        foreach ($items as $item) {
            $path = date('Y/m/d/His/', strtotime($item['date_add']));
            $file = new File($media.$item['cover']);
            $file->move($media.$path);
            $this->addSql('
                UPDATE
                    `item`
                SET
                    `cover` = ?
                WHERE
                    `id` = ?',
                $path.$file->getBasename(),
                $item['id']
            );
        }

        // move images
        $images = $this->connection->fetchAll('
            SELECT
                im.`id`,
                im.`source`,
                i.`date_add`
            FROM
                `item` AS `i`
            INNER JOIN
                `image` AS `im`
                ON
                    im.`item` = i.`id`'
        );
        foreach ($images as $image) {
            $path = date('Y/m/d/His/', strtotime($image['date_add']));
            $file = new File($media.$image['source']);
            $file->move($media.$path);
            $this->addSql('
                UPDATE
                    `image`
                SET
                    `source` = ?
                WHERE
                    `id` = ?',
                $path.$file->getBasename(),
                $image['id']
            );
        }

        // skip if no data
        $this->skipIf(!($items && $images), 'No data to migrate');
    }

    public function down(Schema $schema)
    {
        // the down migration is not need
        $this->skipIf(true, 'The down migration is not need');
    }
}