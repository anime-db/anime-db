<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AnimeDb\Bundle\AppBundle\Entity\Notice;

/**
 * Propose to update the application
 *
 * @package AnimeDb\Bundle\AppBundle\Command
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ProposeUpdateCommand extends ContainerAwareCommand
{
    /**
     * Interval offers updates
     *
     * 30 days
     *
     * @var integer
     */
    const INERVAL_UPDATE = 2592000;

    /**
     * Interval notification
     *
     * 5 days
     *
     * @var integer
     */
    const INERVAL_NOTIFICATION = 432000;

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Command.Command::configure()
     */
    protected function configure()
    {
        $this->setName('animedb:propose-update')
            ->setDescription('Propose to update the application');
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Command.Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $root = $this->getContainer()->getParameter('kernel.root_dir').'/../';
        $time = filemtime($root.'composer.json');

        if (file_exists($root.'composer.lock')) {
            $time = max($time, filemtime($root.'composer.lock'));
        }

        // need update
        if ($time+self::INERVAL_UPDATE < time()) {
            $output->writeln('Application must be updated');

            // send notice
            $notice = new Notice();
            $notice->setMessage(
                $this->getContainer()->get('templating')->render('AnimeDbAppBundle:Notice:propose_update.html.twig')
            );
            $em = $this->getContainer()->get('doctrine.orm.entity_manager');
            $em->persist($notice);
            $em->flush();

            touch($root.'composer.json', time()+self::INERVAL_NOTIFICATION-self::INERVAL_UPDATE);
        } else {
            $output->writeln('Application is already updated');
        }
    }
}