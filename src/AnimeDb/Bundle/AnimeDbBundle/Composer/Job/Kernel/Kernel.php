<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Kernel;

use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Job;
use Composer\Package\PackageInterface;
use AnimeDb\Bundle\AnimeDbBundle\Manipulator\Kernel as KernelManipulator;

/**
 * Kernel
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Kernel
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class Kernel extends Job
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
     * @var \Composer\Package\PackageInterface
     */
    protected $package;

    /**
     * Manipulator
     *
     * @var \AnimeDb\Bundle\AnimeDbBundle\Manipulator\Kernel
     */
    protected $manipulator;

    /**
     * Construct
     *
     * @param \Composer\Package\PackageInterface $package
     */
    public function __construct(PackageInterface $package)
    {
        $this->package = $package;
        $this->manipulator = new KernelManipulator();
    }
}