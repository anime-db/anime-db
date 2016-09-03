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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeliverEventsCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testConfigure()
    {
        $command = new DeliverEventsCommand();
        $this->assertEquals('animedb:deliver-events', $command->getName());
        $this->assertNotEmpty($command->getDescription());
    }

    public function testExecute()
    {
        /* @var $input \PHPUnit_Framework_MockObject_MockObject|InputInterface */
        $input = $this->getMock('\Symfony\Component\Console\Input\InputInterface');

        /* @var $output \PHPUnit_Framework_MockObject_MockObject|OutputInterface */
        $output = $this->getMock('\Symfony\Component\Console\Output\OutputInterface');

        $dispatcher = $this
            ->getMockBuilder('\AnimeDb\Bundle\AnimeDbBundle\Event\Dispatcher')
            ->disableOriginalConstructor()
            ->getMock();
        $dispatcher
            ->expects($this->once())
            ->method('shippingDeferredEvents');

        $container = $this->getMock('\Symfony\Component\DependencyInjection\ContainerInterface');
        $container
            ->expects($this->once())
            ->method('get')
            ->with('anime_db.event_dispatcher')
            ->will($this->returnValue($dispatcher));

        $command = new DeliverEventsCommand();
        $command->setContainer($container);
        $command->run($input, $output);
    }
}
