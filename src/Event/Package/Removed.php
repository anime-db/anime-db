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

use Symfony\Component\EventDispatcher\Event;
use Composer\Package\Package;

/**
 * Event thrown when the package is removed
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Event\Package
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Removed extends Event
{
    /**
     * Package
     *
     * @var \Composer\Package\Package
     */
    protected $package;

    /**
     * Construct
     *
     * @param \Composer\Package\Package $package
     */
    public function __construct(Package $package)
    {
        $this->package = $package;
    }

    /**
     * Get package
     *
     * @return \Composer\Package\Package
     */
    public function getPackage()
    {
        return $this->package;
    }
}