<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AnimeDb\Bundle\CatalogBundle\Entity\Task as TaskEntity;

/**
 * Task repository
 *
 * @package AnimeDb\Bundle\CatalogBundle\Repository
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Task extends EntityRepository
{
    /**
     * Maximum standby time
     *
     * Between scans tasks can interpose new task.
     * If no limit standby scheduler can not handle the new task before
     * the arrival of the start time of the next task.
     * For example the scheduler can expect a few days before the execution of the tasks
     * that must be performed every hour.
     * 
     * @var integer
     */
    const MAX_STANDBY_TIME = 3600;

    /**
     * Get next task
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Task|null
     */
    public function getNextTask()
    {
        return $this->getEntityManager()->createQuery('
            SELECT
                t
            FROM
                AnimeDbCatalogBundle:Task t
            WHERE
                t.status = :status AND t.next_run <= :time
            ORDER BY
                t.next_run ASC
        ')
            ->setMaxResults(1)
            ->setParameter('status', TaskEntity::STATUS_ENABLED)
            ->setParameter('time', date('Y-m-d H:i:s'))
            ->getOneOrNullResult();
    }

    /**
     * Get waiting time for the next task
     *
     * @return integer
     */
    public function getWaitingTime()
    {
        // get next task
        $task = $this->getEntityManager()->createQuery('
            SELECT
                t
            FROM
                AnimeDbCatalogBundle:Task t
            WHERE
                t.status = :status
            ORDER BY
                t.next_run ASC
        ')
            ->setMaxResults(1)
            ->setParameter('status', TaskEntity::STATUS_ENABLED)
            ->getOneOrNullResult();

        // task is exists
        if ($task instanceof TaskEntity) {
            $task_time = $task->getNextRun()->getTimestamp() - time();
            if ($task_time > 0) {
                return min($task_time, self::MAX_STANDBY_TIME);
            } else {
                return 0; // need run now
            }
        }

        return self::MAX_STANDBY_TIME;
    }
}