<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Notify\Project;

use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Job;
use AnimeDb\Bundle\AnimeDbBundle\Event\Project\StoreEvents;
use AnimeDb\Bundle\AnimeDbBundle\Event\Project\Updated as Event;

/**
 * Job: Notice that the project has been updated
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Notify\Project
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Updated extends Job
{
    /**
     * (non-PHPdoc)
     * @see AnimeDb\Bundle\AnimeDbBundle\Composer\Job.Job::execute()
     */
    public function execute()
    {
        $this->getContainer()->getEventDispatcher()->dispatch(
            StoreEvents::UPDATED,
            new Event($this->getContainer()->getSimpleCopyOfPacket($this->getPackage()))
        );
    }
}