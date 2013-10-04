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
    const UPDATE_ITEM = 'anime_db.update_item_on_storage';

    /**
     * Event thrown when a new item is detected
     *
     * @var string
     */
    const NEW_ITEM = 'anime_db.new_item_on_storage';

    /**
     * Event thrown when item is removed
     *
     * @var string
     */
    const DELETE_ITEM = 'anime_db.delete_item_on_storage';
}