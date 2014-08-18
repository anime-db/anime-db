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
use Composer\Package\RootPackage;

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
     * New package
     *
     * @var \Composer\Package\RootPackage
     */
    protected $new_package;

    /**
     * Old package
     *
     * @var \Composer\Package\RootPackage
     */
    protected $old_package;

    /**
     * Construct
     *
     * @param string $path
     * @param \Composer\Package\RootPackage $new_package
     * @param \Composer\Package\RootPackage $old_package
     */
    public function __construct($path, $new_package, $old_package)
    {
        $this->path = $path;
        $this->new_package = $new_package;
        $this->old_package = $old_package;
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
     * Get new package
     *
     * @return \Composer\Package\RootPackage
     */
    public function getNewPackage()
    {
        return $this->new_package;
    }

    /**
     * Get old package
     *
     * @return \Composer\Package\RootPackage
     */
    public function getOldPackage()
    {
        return $this->old_package;
    }
}