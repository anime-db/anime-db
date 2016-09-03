<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\AnimeDbBundle\Event\Project;

/**
 * Project event names.
 */
final class StoreEvents
{
    /**
     * Event thrown when the project is installed.
     *
     * @var string
     */
    const INSTALLED = 'anime_db.project.installed';

    /**
     * Event thrown when the project is updated.
     *
     * @var string
     */
    const UPDATED = 'anime_db.project.updated';
}
