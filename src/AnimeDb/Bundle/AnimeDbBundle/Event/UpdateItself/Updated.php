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
 * Event thrown when the application updated
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Event\UpdateItself
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Updated extends Event
{
    /**
     * Package
     *
     * @var \Composer\Package\RootPackage
     */
    protected $package;

    /**
     * Construct
     *
     * @param \Composer\Package\RootPackage $package
     */
    public function __construct($package)
    {
        $this->package = $package;
    }

    /**
     * Get package
     *
     * @return string
     */
    public function getPackage()
    {
        return $this->package;
    }
}