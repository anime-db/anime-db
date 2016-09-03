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
use AnimeDb\Bundle\AnimeDbBundle\Manipulator\Kernel;

/**
 * Job: Add package to kernel
 */
class Add extends Job
{
    /**
     * @var int
     */
    const PRIORITY = self::PRIORITY_INSTALL;

    public function execute()
    {
        if ($bundle = $this->getPackageBundle()) {
            /* @var $manipulator Kernel */
            $manipulator = $this->getContainer()->getManipulator('kernel');
            $manipulator->addBundle($bundle);
        }
    }
}
