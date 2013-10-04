<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Event\Storage;

/**
 * Storages event names
 *
 * @package AnimeDb\Bundle\CatalogBundle\Event\Storage
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
final class StoreEvents
{
    /**
     * Event thrown when a change is detected item
     *
     * @var string
     */
    const UPDATE_ITEM_FILES = 'anime_db.storage.update_item_files';

    /**
     * Event thrown when a new item files is detected
     *
     * @var string
     */
    const DETECTED_NEW_FILES = 'anime_db.storage.detected_new_files';

    /**
     * Event thrown when item is removed
     *
     * @var string
     */
    const DELETE_ITEM_FILES = 'anime_db.storage.delete_item_files';
}