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

use AnimeDb\Bundle\AnimeDbBundle\Command\UpdateCommand;

/**
 * Test update command
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Tests\Command
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class UpdateCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test configure
     */
    public function testConfigure()
    {
        $command = new UpdateCommand();
        $this->assertEquals('animedb:update', $command->getName());
        $this->assertNotEmpty($command->getDescription());
    }

    /**
     * Test execute
     */
    public function testExecute()
    {
        // TODO need write the test of execute command
        $this->markTestSkipped('Need write the test of execute command');

        $input = $this->getMock('\Symfony\Component\Console\Input\InputInterface');
        $output = $this->getMock('\Symfony\Component\Console\Output\OutputInterface');
        $dispatcher = $this->getMock('\AnimeDb\Bundle\AnimeDbBundle\Event\Dispatcher');
        $container = $this->getMock('\Symfony\Component\DependencyInjection\ContainerInterface');
        $container
            ->expects($this->any())
            ->method('get')
            ->with('event_dispatcher')
            ->willReturn($dispatcher);

        $command = new UpdateCommand();
        $command->setContainer($container);
        $command->run($input, $output);
    }
}