<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Event\Project;

use Symfony\Component\EventDispatcher\Event;
use Composer\Package\Package;

/**
 * Event thrown when the project is updated
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Event\Project
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Updated extends Event
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
    public function __construct($package)
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