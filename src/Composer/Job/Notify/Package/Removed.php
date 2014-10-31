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
use AnimeDb\Bundle\AnimeDbBundle\Event\Package\StoreEvents;
use AnimeDb\Bundle\AnimeDbBundle\Event\Package\Removed as Event;

/**
 * Job: Notice that the package has been removed
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Notify\Package
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Removed extends Job
{
    /**
     * (non-PHPdoc)
     * @see AnimeDb\Bundle\AnimeDbBundle\Composer\Job.Job::execute()
     */
    public function execute()
    {
        $this->getContainer()->getEventDispatcher()->dispatch(
            StoreEvents::REMOVED,
            new Event($this->getPackageCopy())
        );
    }
}
