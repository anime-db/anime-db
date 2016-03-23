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

/**
 * Job: Remove package from kernel
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Kernel
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Remove extends Job
{
    /**
     * Job priority
     *
     * @var integer
     */
    const PRIORITY = self::PRIORITY_INSTALL;

    /**
     * Package bundle name
     *
     * @var string|null
     */
    protected $bundle;

    public function register()
    {
        // get the bundle name before remove package, because then it would impossible to do
        $this->bundle = $this->getPackageBundle();
    }

    public function execute()
    {
        if ($this->bundle !== null) {
            /* @var $manipulator \AnimeDb\Bundle\AnimeDbBundle\Manipulator\Kernel */
            $manipulator = $this->getContainer()->getManipulator('kernel');
            $manipulator->removeBundle($this->bundle);
        }
    }
}
