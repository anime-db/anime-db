<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Tests\Composer\Job;

use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Container;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Job;

/**
 * Test job container
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Tests\Composer\Job
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Container
     */
    protected $container;

    protected function setUp()
    {
        $this->container = new Container(__DIR__.'/../../../../app/');
    }

    public function testGetEventDispatcher()
    {
        $dispatcher = $this->container->getEventDispatcher();
        $this->assertInstanceOf('\AnimeDb\Bundle\AnimeDbBundle\Event\Dispatcher', $dispatcher);
        $this->assertEquals($dispatcher, $this->container->getEventDispatcher());
    }

    /**
     * @return array
     */
    public function getManipulators()
    {
        return [
            [
                'composer',
                '\AnimeDb\Bundle\AnimeDbBundle\Manipulator\Composer'
            ],
            [
                'config',
                '\AnimeDb\Bundle\AnimeDbBundle\Manipulator\Config'
            ],
            [
                'kernel',
                '\AnimeDb\Bundle\AnimeDbBundle\Manipulator\Kernel'
            ],
            [
                'routing',
                '\AnimeDb\Bundle\AnimeDbBundle\Manipulator\Routing'
            ]
        ];
    }

    /**
     * @dataProvider getManipulators
     *
     * @param string $name
     * @param string $class
     */
    public function testGetManipulator($name, $class)
    {
        $manipulator = $this->container->getManipulator($name);
        $this->assertInstanceOf($class, $manipulator);
        $this->assertEquals($manipulator, $this->container->getManipulator($name));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetManipulatorFailed()
    {
        $this->container->getManipulator('undefined');
    }

    public function testJobs()
    {
        $order_execution = [];

        // priority task will sort in reverse order
        $this->container->addJob($this->getJob(2, $order_execution));
        $this->container->addJob($this->getJob(1, $order_execution));
        $this->container->addJob($this->getJob(3, $order_execution));
        $this->container->execute();

        $this->assertEquals([1, 2, 3], $order_execution);
    }

    /**
     * @param int $priority
     * @param array $order
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|Job
     */
    protected function getJob($priority, array &$order)
    {
        $job = $this
            ->getMockBuilder('\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Job')
            ->disableOriginalConstructor()
            ->getMock();
        $job
            ->expects($this->once())
            ->method('setContainer')
            ->with($this->container);
        $job
            ->expects($this->once())
            ->method('register');
        $job
            ->expects($this->any())
            ->method('getPriority')
            ->will($this->returnValue($priority));
        $job
            ->expects($this->once())
            ->method('execute')
            ->will($this->returnCallback(function () use ($priority, &$order) {
                $order[] = $priority;
            }));

        return $job;
    }

    public function testExecuteCommand()
    {
        ob_start();
        $this->container->executeCommand('--version --no-ansi');
        exec('php '.__DIR__.'/../../../../app/console --version --no-ansi', $output);
        $this->assertEquals($output[0].PHP_EOL, ob_get_clean());
    }
}
