<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\AnimeDbBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Shipping deferred events.
 */
class DeliverEventsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('animedb:deliver-events')
            ->setDescription('Shipping deferred events');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return bool
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('anime_db.event_dispatcher')->shippingDeferredEvents();
    }
}
