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

use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Routing\Routing as BaseRouting;

/**
 * Job: Remove package from routing
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Routing
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Remove extends BaseRouting
{
    /**
     * (non-PHPdoc)
     * @see AnimeDb\Bundle\AnimeDbBundle\Composer\Job.Job::execute()
     */
    public function execute()
    {
        $this->getContainer()->getManipulator('routing')->removeResource($this->getNodeName());
    }
}