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
use Composer\Package\RootPackageInterface;

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
     * @var \Composer\Package\RootPackageInterface
     */
    protected $new_package;

    /**
     * Old package
     *
     * @var \Composer\Package\RootPackageInterface
     */
    protected $old_package;

    /**
     * Construct
     *
     * @param string $path
     * @param \Composer\Package\RootPackageInterface $new_package
     * @param \Composer\Package\RootPackageInterface $old_package
     */
    public function __construct($path, RootPackageInterface $new_package, RootPackageInterface $old_package)
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
     * @return \Composer\Package\RootPackageInterface
     */
    public function getNewPackage()
    {
        return $this->new_package;
    }

    /**
     * Get old package
     *
     * @return \Composer\Package\RootPackageInterface
     */
    public function getOldPackage()
    {
        return $this->old_package;
    }
}