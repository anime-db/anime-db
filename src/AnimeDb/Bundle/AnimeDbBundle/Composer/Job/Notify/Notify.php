<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Notify;

use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Job;
use Composer\Package\PackageInterface;

/**
 * Package
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Notify
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class Notify extends Job
{
    /**
     * Package
     *
     * @var \Composer\Package\PackageInterface
     */
    protected $package;

    /**
     * Construct
     *
     * @param \Composer\Package\PackageInterface $package
     */
    public function __construct(PackageInterface $package)
    {
        $this->package = $package;
    }
}