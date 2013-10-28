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

use Symfony\Component\EventDispatcher\Event;

/**
 * Event thrown when the application downloaded
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Event\UpdateItself
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Downloaded extends Event
{
    /**
     * Path to store a new application
     *
     * @var string
     */
    protected $path;

    /**
     * New version
     *
     * @var string
     */
    protected $version;

    /**
     * Construct
     *
     * @param string $path
     * @param string $version
     */
    public function __construct($path, $version)
    {
        $this->path = $path;
        $this->version = $version;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
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