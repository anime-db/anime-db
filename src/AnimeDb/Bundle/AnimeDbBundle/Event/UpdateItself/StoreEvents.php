<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Event\UpdateItself;

/**
 * Update itself event names
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Event\UpdateItself
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
final class StoreEvents
{
    /**
     * Event thrown when the application download
     *
     * @var string
     */
    const DOWNLOADED = 'anime_db.update_itself.downloaded';

    /**
     * Event thrown when the application updated
     *
     * @var string
     */
    const UPDATED = 'anime_db.update_itself.updated';
}