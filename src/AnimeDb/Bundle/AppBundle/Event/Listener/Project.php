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
use AnimeDb\Bundle\AnimeDbBundle\Event\Project\Updated as UpdatedEvent;
use AnimeDb\Bundle\AppBundle\Command\ProposeUpdateCommand;

/**
 * Project listener
 *
 * @package AnimeDb\Bundle\AppBundle\Event\Listener
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Project
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
     * Update next run date for the propose update task
     *
     * @param \AnimeDb\Bundle\AnimeDbBundle\Event\Project\Updated $event
     */
    public function onUpdated(UpdatedEvent $event)
    {
        /* @var $task \AnimeDb\Bundle\AppBundle\Entity\Task */
        $task = $this->em
            ->getRepository('AnimeDbAppBundle:Task')
            ->findOneByCommand('animedb:propose-update');

        $next_run = time()+ProposeUpdateCommand::INERVAL_UPDATE;
        $next_run = mktime(1, 0, 0, date('m', $next_run), date('d', $next_run), date('y', $next_run));
        $task->setNextRun(new \DateTime(date('Y-m-d H:i:s', $next_run)));

        $this->em->persist($task);
        $this->em->flush();
    }
}