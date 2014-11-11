<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Tests\Command;

use AnimeDb\Bundle\AnimeDbBundle\Command\DeliverEventsCommand;

/**
 * Test deliver events command
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Tests\Command
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class DeliverEventsCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test configure
     */
    public function testConfigure()
    {
        $command = new DeliverEventsCommand();
        $this->assertEquals('animedb:deliver-events', $command->getName());
        $this->assertNotEmpty($command->getDescription());
    }

    /**
     * Test execute
     */
    public function testExecute()
    {
        $input = $this->getMock('\Symfony\Component\Console\Input\InputInterface');
        $output = $this->getMock('\Symfony\Component\Console\Output\OutputInterface');
        $dispatcher = $this->getMockBuilder('\AnimeDb\Bundle\AnimeDbBundle\Event\Dispatcher')
            ->disableOriginalConstructor()
            ->getMock();
        $container = $this->getMock('\Symfony\Component\DependencyInjection\ContainerInterface');
        $container
            ->expects($this->once())
            ->method('get')
            ->with('anime_db.event_dispatcher')
            ->willReturn($dispatcher);
        $dispatcher
            ->expects($this->once())
            ->method('shippingDeferredEvents');

        $command = new DeliverEventsCommand();
        $command->setContainer($container);
        $command->run($input, $output);
    }
}
