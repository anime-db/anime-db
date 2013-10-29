<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Notify\Package;

use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Job;
use Composer\Package\PackageInterface;

/**
 * Package
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Notify\Package
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class Package extends Job
{
    /**
     * Package
     *
     * @var \Composer\Package\PackageInterface
     */
    protected $package;

    /**
     * Set package
     *
     * @param \Composer\Package\PackageInterface $package
     */
    public function setPackage(PackageInterface $package)
    {
        $this->package = $package;
    }
}