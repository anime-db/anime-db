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
use Doctrine\ORM\EntityRepository;

/**
 * Task Scheduler
 *
 * @package AnimeDB\Bundle\CatalogBundle\Command
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class TaskSchedulerCommand extends ContainerAwareCommand
{
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
        /* @var $repository \AnimeDB\Bundle\CatalogBundle\Repository\Task */
        $repository = $em->getRepository('AnimeDBCatalogBundle:Task');

        // output streams
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $streams = '>null 2>&1';
        } else {
            $streams = '>/dev/null 2>&1';
        }

        $output->writeln('Task Scheduler');

        while (true) {
            $task = $repository->getNextTask();

            // task is exists
            if ($task instanceof Task) {
                $output->writeln('Run <info>'.$task->getCommand().'</info>');
                exec($console.' '.$task->getCommand().' '.$streams.' &');

                // update information on starting
                $task->executed();
                $em->persist($task);
                $em->flush();
            }

            // standby for the next task
            $time = $repository->getWaitingTime();
            if ($time) {
                $output->writeln('Wait <comment>'.$time.'</comment> s.');
                sleep($time);
            }

            unset($task);
            gc_collect_cycles();
        }
    }
}