<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Routing;

use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Job;
use AnimeDb\Bundle\AnimeDbBundle\Manipulator\Routing;

/**
 * Job: Remove package from routing.
 */
class Remove extends Job
{
    /**
     * @var int
     */
    const PRIORITY = self::PRIORITY_INSTALL;

    public function execute()
    {
        /* @var $manipulator Routing */
        $manipulator = $this->getContainer()->getManipulator('routing');
        $manipulator->removeResource($this->getRoutingNodeName());
    }
}
