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

use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Notify\Notify as BaseNotify;
use AnimeDb\Bundle\AnimeDbBundle\Event\Package\StoreEvents;
use AnimeDb\Bundle\AnimeDbBundle\Event\Package\Removed as Event;

/**
 * Job: Notice that the package has been removed
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Notify
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Removed extends BaseNotify
{
    /**
     * (non-PHPdoc)
     * @see AnimeDb\Bundle\AnimeDbBundle\Composer\Job.Job::execute()
     */
    public function execute()
    {
        $dispatcher = $this->container->getKernel()->getContainer()->get('event_dispatcher');
        $dispatcher->dispatch(StoreEvents::REMOVED, new Event($this->package));
    }
}