<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Notify\Package;

use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Job;
use AnimeDb\Bundle\AnimeDbBundle\Event\Package\StoreEvents;
use AnimeDb\Bundle\AnimeDbBundle\Event\Package\Installed as Event;

/**
 * Job: Notice that the package has been installed.
 */
class Installed extends Job
{
    public function execute()
    {
        $this->getContainer()->getEventDispatcher()->dispatch(
            StoreEvents::INSTALLED,
            new Event($this->getPackageCopy())
        );
    }
}
