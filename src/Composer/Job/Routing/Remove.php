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

/**
 * Job: Remove package from routing
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Routing
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Remove extends Job
{
    /**
     * @var integer
     */
    const PRIORITY = self::PRIORITY_INSTALL;

    public function execute()
    {
        /* @var $manipulator \AnimeDb\Bundle\AnimeDbBundle\Manipulator\Routing */
        $manipulator = $this->getContainer()->getManipulator('routing');
        $manipulator->removeResource($this->getRoutingNodeName());
    }
}
