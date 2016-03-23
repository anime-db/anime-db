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
 * Event thrown when the package is installed
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Event\Package
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Installed extends Event
{
    /**
     * @var Package
     */
    protected $package;

    /**
     * @param Package $package
     */
    public function __construct(Package $package)
    {
        $this->package = $package;
    }

    /**
     * @return Package
     */
    public function getPackage()
    {
        return $this->package;
    }
}
