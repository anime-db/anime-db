<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\AnimeDbBundle\Tests\Composer\Job;

use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Container;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Job;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Notify\Package\Installed as InstalledPackage;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Notify\Package\Removed as RemovedPackage;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Notify\Package\Updated as UpdatedPackage;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Notify\Project\Installed as InstalledProject;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Notify\Project\Updated as UpdatedProject;
use AnimeDb\Bundle\AnimeDbBundle\Event\Package\StoreEvents as StoreEventsPackage;
use AnimeDb\Bundle\AnimeDbBundle\Event\Project\Installed;
use AnimeDb\Bundle\AnimeDbBundle\Event\Project\StoreEvents as StoreEventsProject;

class NotifyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function getJobs()
    {
        return [
            [
                StoreEventsPackage::INSTALLED,
                '\AnimeDb\Bundle\AnimeDbBundle\Event\Package\Installed',
                function ($package) {
                    return new InstalledPackage($package);
                },
            ],
            [
                StoreEventsPackage::REMOVED,
                '\AnimeDb\Bundle\AnimeDbBundle\Event\Package\Removed',
                function ($package) {
                    return new RemovedPackage($package);
                },
            ],
            [
                StoreEventsPackage::UPDATED,
                '\AnimeDb\Bundle\AnimeDbBundle\Event\Package\Updated',
                function ($package) {
                    return new UpdatedPackage($package);
                },
            ],
            [
                StoreEventsProject::INSTALLED,
                '\AnimeDb\Bundle\AnimeDbBundle\Event\Project\Installed',
                function ($package) {
                    return new InstalledProject($package);
                },
            ],
            [
                StoreEventsProject::UPDATED,
                '\AnimeDb\Bundle\AnimeDbBundle\Event\Project\Updated',
                function ($package) {
                    return new UpdatedProject($package);
                },
            ],
        ];
    }

    /**
     * @dataProvider getJobs
     *
     * @param string $event_name
     * @param string $event_class
     * @param \Closure $get_job
     */
    public function testExecute($event_name, $event_class, \Closure $get_job)
    {
        $extra = [
            'anime-db-routing' => '',
            'anime-db-config' => '',
            'anime-db-bundle' => '',
            'anime-db-migrations' => '',
        ];
        $that = $this;
        $package = $this
            ->getMockBuilder('\Composer\Package\Package')
            ->disableOriginalConstructor()
            ->getMock();
        $package
            ->expects($this->atLeastOnce())
            ->method('getExtra')
            ->will($this->returnValue($extra));
        $package
            ->expects($this->atLeastOnce())
            ->method('getName')
            ->will($this->returnValue('foo'));
        $package
            ->expects($this->atLeastOnce())
            ->method('getVersion')
            ->will($this->returnValue('1'));
        $package
            ->expects($this->atLeastOnce())
            ->method('getPrettyVersion')
            ->will($this->returnValue('1.0'));
        $package
            ->expects($this->atLeastOnce())
            ->method('getType')
            ->will($this->returnValue('library'));

        $dispatcher = $this
            ->getMockBuilder('\AnimeDb\Bundle\AnimeDbBundle\Event\Dispatcher')
            ->disableOriginalConstructor()
            ->getMock();
        $dispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($event_name)
            ->will($this->returnCallback(function ($name, $event) use ($that, $package, $extra, $event_class) {
                $that->assertInstanceOf($event_class, $event);
                /* @var $event Installed */
                $that->assertInstanceOf('\Composer\Package\Package', $event->getPackage());
                $that->assertNotEquals($package, $event->getPackage());
                $that->assertEquals('foo', $event->getPackage()->getName());
                $that->assertEquals('1', $event->getPackage()->getVersion());
                $that->assertEquals('1.0', $event->getPackage()->getPrettyVersion());
                $that->assertEquals('library', $event->getPackage()->getType());
                $that->assertEquals($extra, $event->getPackage()->getExtra());
            }));
        /* @var $container \PHPUnit_Framework_MockObject_MockObject|Container */
        $container = $this
            ->getMockBuilder('\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Container')
            ->disableOriginalConstructor()
            ->getMock();
        $container
            ->expects($this->once())
            ->method('getEventDispatcher')
            ->will($this->returnValue($dispatcher));

        /* @var $job Job */
        $job = $get_job($package);
        $job->setContainer($container);
        $job->execute();
    }
}
