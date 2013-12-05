<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AppBundle\Event\Listener;

use Doctrine\ORM\EntityManager;
use AnimeDb\Bundle\AnimeDbBundle\Event\Package\Installed as InstalledEvent;
use AnimeDb\Bundle\AnimeDbBundle\Event\Package\Removed as RemovedEvent;
use AnimeDb\Bundle\AnimeDbBundle\Event\Package\Updated as UpdatedEvent;

/**
 * Package listener
 *
 * @package AnimeDb\Bundle\AppBundle\Event\Listener
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Package
{
    /**
     * Entity manager
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * Construct
     *
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Update plugin data
     *
     * @param \AnimeDb\Bundle\AnimeDbBundle\Event\Project\Updated $event
     */
    public function onUpdated(UpdatedEvent $event)
    {
        // TODO update plugin data
    }

    /**
     * Registr plugin
     *
     * @param \AnimeDb\Bundle\AnimeDbBundle\Event\Project\Installed $event
     */
    public function onInstalled(InstalledEvent $event)
    {
        // TODO registr plugin in db
    }

    /**
     * Unregistr plugin
     *
     * @param \AnimeDb\Bundle\AnimeDbBundle\Event\Project\Removed $event
     */
    public function onRemoved(RemovedEvent $event)
    {
        // TODO delete plugin from db
    }
}