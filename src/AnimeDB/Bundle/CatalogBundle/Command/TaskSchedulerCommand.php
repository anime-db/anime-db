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
        while (true) {
            // TODO get tasks and run this
            sleep(86400);
        }
    }
}