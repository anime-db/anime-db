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
     * @var integer
     */
    const MAX_SLEEP_TIME = 86400;

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
        $php = $finder->find();

        // output streams
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $streams = '> null 2 > &1';
        } else {
            $streams = '> /dev/null 2 > &1';
        }

        $output->writeln('Task Scheduler');

        while (true) {
            $task = $this->getNextTask();

            // task is exists
            if ($task instanceof Task) {
                $output->writeln('Run <info>'.$task->getCommand().'</info>');
                exec($php.' '.__DIR__.'/../../../../../app/console '.$task->getCommand().' '.$streams.' &');
            }

            // fall asleep waiting for next task
            $time = $this->getSleepTime();
            $output->writeln('Sleep <comment>'.$time.'</comment> s.');
            sleep($time);
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
     * Get sleep time
     *
     * @return integer
     */
    protected function getSleepTime()
    {
        $current = time();
        $time = self::MAX_SLEEP_TIME;
        $repository = $this->getContainer()->get('doctrine')
            ->getRepository('AnimeDBCatalogBundle:Task');

        // get next task
        $task = $repository->createQueryBuilder('t')
            ->where('t.status = :status AND t.next_run > :time')
            ->setParameter('status', Task::STATUS_ENABLED)
            ->setParameter('time', date('Y-m-d H:m:s', $current))
            ->orderBy('t.next_run', 'ASC')
            ->getQuery()
            ->getOneOrNullResult();

        // task is exists
        if ($task instanceof Task) {
            $time = $task->getNextRun()->getTimestamp() - $current;
        }

        return min($time, self::MAX_SLEEP_TIME);
    }
}