<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\Bundle\CatalogBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AnimeDB\Bundle\CatalogBundle\Entity\Task;
use Symfony\Component\Process\PhpExecutableFinder;

/**
 * Task Scheduler
 *
 * @package AnimeDB\Bundle\CatalogBundle\Command
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class TaskSchedulerCommand extends ContainerAwareCommand
{
    /**
     * Maximum sleep time
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
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Command.Command::configure()
     */
    protected function configure()
    {
        $this->setName('animedb:task-scheduler')
            ->setDescription('Task Scheduler');
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Command.Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        // exit if disabled
        if (!$this->getContainer()->getParameter('task-scheduler')['enabled']) {
            return null;
        }

        // path to php executable
        $finder = new PhpExecutableFinder();
        $console = $finder->find().' '.__DIR__.'/../../../../../app/console';

        $em = $this->getContainer()->get('doctrine')->getManager();

        // output streams
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $streams = '>null 2>&1';
        } else {
            $streams = '>/dev/null 2>&1';
        }

        $output->writeln('Task Scheduler');

        while (true) {
            $task = $this->getNextTask();

            // task is exists
            if ($task instanceof Task) {
                $output->writeln('Run <info>'.$task->getCommand().'</info>');
                exec($console.' '.$task->getCommand().' '.$streams.' &');

                // обнавляем информацию о запуске
                $task->executed();
                $em->persist($task);
                $em->flush();
            }

            // standby for the next task
            $time = $this->getWaitingTime();
            if ($time) {
                $output->writeln('Sleep <comment>'.$time.'</comment> s.');
                sleep($time);
            }
        }
    }

    /**
     * Get next task
     *
     * @return \AnimeDB\Bundle\CatalogBundle\Entity\Task|null
     */
    protected function getNextTask()
    {
        $repository = $this->getContainer()->get('doctrine')
            ->getRepository('AnimeDBCatalogBundle:Task');

        return $repository->createQueryBuilder('t')
                ->where('t.status = :status AND t.next_run <= :time')
                ->setParameter('status', Task::STATUS_ENABLED)
                ->setParameter('time', date('Y-m-d H:m:s'))
                ->orderBy('t.next_run', 'ASC')
                ->getQuery()
                ->getOneOrNullResult();
    }

    /**
     * Get waiting time for the next task
     *
     * @return integer
     */
    protected function getWaitingTime()
    {
        $repository = $this->getContainer()->get('doctrine')
            ->getRepository('AnimeDBCatalogBundle:Task');

        // get next task
        $task = $repository->createQueryBuilder('t')
            ->where('t.status = :status')
            ->setParameter('status', Task::STATUS_ENABLED)
            ->orderBy('t.next_run', 'ASC')
            ->getQuery()
            ->getOneOrNullResult();

        // task is exists
        if ($task instanceof Task) {
            $task_time = $task->getNextRun()->getTimestamp() - time();
            if ($task_time > 0) {
                return min($task_time, self::MAX_STANDBY_TIME);
            } else {
                return 0;
            }
        }

        return self::MAX_STANDBY_TIME;
    }
}