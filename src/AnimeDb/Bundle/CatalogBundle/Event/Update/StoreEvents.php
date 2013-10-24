<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Event\Update;

/**
 * Update event names
 *
 * @package AnimeDb\Bundle\CatalogBundle\Event\Update
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
final class StoreEvents
{
    /**
     * Event thrown when the application download
     *
     * @var string
     */
    const APPLICATION_DOWNLOADED = 'anime_db.update.application_downloaded';

    /**
     * Event thrown when the application updated
     *
     * @var string
     */
    const APPLICATION_UPDATED = 'anime_db.update.application_updated';

    /**
     * Event thrown when the plugin download
     *
     * @var string
     */
    const PLUGIN_DOWNLOADED = 'anime_db.update.plugin_download';

    /**
     * Event thrown when the plugin updated
     *
     * @var string
     */
    const PLUGIN_UPDATED = 'anime_db.update.plugin_updated';
}