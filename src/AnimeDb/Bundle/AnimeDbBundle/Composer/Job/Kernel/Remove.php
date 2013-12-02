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
use AnimeDb\Bundle\AnimeDbBundle\Manipulator\Kernel as KernelManipulator;

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
     * (non-PHPdoc)
     * @see AnimeDb\Bundle\AnimeDbBundle\Composer\Job.Job::execute()
     */
    public function execute()
    {
        if ($bundle = $this->getContainer()->getPackageBundle($this->getPackage())) {
            $manipulator = new KernelManipulator();
            $manipulator->removeBundle($bundle);
        }
    }
}