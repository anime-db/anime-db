<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Tests\Event;

use AnimeDb\Bundle\AnimeDbBundle\Event\Dispatcher;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Test Dispatcher
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Tests\Event
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class DispatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Filesystem
     *
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $fs;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->fs = new Filesystem();
    }

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        parent::setUp();
        $this->fs->remove(__DIR__.'/../'.Dispatcher::EVENTS_DIR);
    }

    /**
     * Test empty driver
     */
    public function testEmptyDriver()
    {
        $dispatcher = new Dispatcher();
        $dispatcher->shippingDeferredEvents();
    }

    /**
     * Test dispatch
     */
    public function testDispatch()
    {
        $event1 = new Event();
        $event1->setName('foo');
        $event2 = new Event();
        $event2->setName('bar');

        $driver = $this->getMock('\Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $driver
            ->expects($this->at(1))
            ->method('dispatch')
            ->with($event1->getName(), $event1)
            ->will($this->returnArgument(1));
        $driver
            ->expects($this->at(0))
            ->method('dispatch')
            ->with($event2->getName(), $event2)
            ->will($this->returnArgument(1));

        $dispatcher = new Dispatcher();
        $dispatcher->setDispatcherDriver($driver);
        $dispatcher->dispatch($event1->getName(), $event1);
        $dispatcher->dispatch($event2->getName(), $event2);
        $dispatcher->shippingDeferredEvents();
    }
}