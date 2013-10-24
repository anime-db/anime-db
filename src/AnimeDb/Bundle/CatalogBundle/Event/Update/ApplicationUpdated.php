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

use Symfony\Component\EventDispatcher\Event;

/**
 * Event thrown when the application updated
 *
 * @package AnimeDb\Bundle\CatalogBundle\Event\Update
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ApplicationUpdated extends Event
{
    /**
     * New version
     *
     * @var string
     */
    protected $version;

    /**
     * Construct
     *
     * @param string $version
     */
    public function __construct($version)
    {
        $this->version = $version;
    }

    /**
     * Get version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }
}