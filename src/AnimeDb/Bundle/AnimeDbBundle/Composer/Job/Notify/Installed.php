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
use AnimeDb\Bundle\AnimeDbBundle\Event\Package\Installed as Event;

/**
 * Job: Notice that the package has been installed
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Notify
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Installed extends BaseNotify
{
    /**
     * (non-PHPdoc)
     * @see AnimeDb\Bundle\AnimeDbBundle\Composer\Job.Job::execute()
     */
    public function execute()
    {
        $dispatcher = $this->getContainer()->getKernel()->getContainer()->get('event_dispatcher');
        $dispatcher->dispatch(StoreEvents::INSTALLED, new Event($this->package));
    }
}