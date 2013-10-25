<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Event\Package;

/**
 * Package event names
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Event\Package
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
final class StoreEvents
{
    /**
     * Event thrown when the package is installed
     *
     * @var string
     */
    const INSTALLED = 'anime_db.package.installed';

    /**
     * Event thrown when the package is removed
     *
     * @var string
     */
    const REMOVED = 'anime_db.package.removed';
}