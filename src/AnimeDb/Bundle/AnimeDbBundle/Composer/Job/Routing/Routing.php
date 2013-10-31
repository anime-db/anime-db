<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Routing;

use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Job;
use Composer\Package\Package;
use AnimeDb\Bundle\AnimeDbBundle\Manipulator\Routing as RoutingManipulator;

/**
 * Routing
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Routing
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class Routing extends Job
{
    /**
     * Job priority
     *
     * @var integer
     */
    const PRIORITY = self::PRIORITY_INSTALL;

    /**
     * Package
     *
     * @var \Composer\Package\Package
     */
    protected $package;

    /**
     * Manipulator
     *
     * @var \AnimeDb\Bundle\AnimeDbBundle\Manipulator\Routing
     */
    protected $manipulator;

    /**
     * Construct
     *
     * @param \Composer\Package\Package $package
     */
    public function __construct(Package $package)
    {
        $this->package = $package;
        $this->manipulator = new RoutingManipulator();
    }
}