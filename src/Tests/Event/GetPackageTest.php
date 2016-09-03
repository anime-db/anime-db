<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Tests\Event\Package;

use AnimeDb\Bundle\AnimeDbBundle\Event\Package\Installed as InstalledPackage;
use AnimeDb\Bundle\AnimeDbBundle\Event\Package\Removed as RemovedPackage;
use AnimeDb\Bundle\AnimeDbBundle\Event\Package\Updated as UpdatedPackage;
use AnimeDb\Bundle\AnimeDbBundle\Event\Project\Installed as InstalledProject;
use AnimeDb\Bundle\AnimeDbBundle\Event\Project\Updated as UpdatedProject;
use AnimeDb\Bundle\AnimeDbBundle\Event\UpdateItself\Updated as UpdatedUpdateItself;

class GetPackageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function getEvents()
    {
        return [
            [
                function ($package) {
                    return new InstalledPackage($package);
                },
                '\Composer\Package\Package'
            ],
            [
                function ($package) {
                    return new RemovedPackage($package);
                },
                '\Composer\Package\Package'
            ],
            [
                function ($package) {
                    return new UpdatedPackage($package);
                },
                '\Composer\Package\Package'
            ],
            [
                function ($package) {
                    return new InstalledProject($package);
                },
                '\Composer\Package\Package'
            ],
            [
                function ($package) {
                    return new UpdatedProject($package);
                },
                '\Composer\Package\Package'
            ],
            [
                function ($package) {
                    return new UpdatedUpdateItself($package);
                },
                '\Composer\Package\RootPackageInterface'
            ]
        ];
    }

    /**
     * @dataProvider getEvents
     *
     * @param \Closure $get_event
     * @param string $package
     */
    public function testGetPackage(\Closure $get_event, $package)
    {
        $package = $this
            ->getMockBuilder($package)
            ->disableOriginalConstructor()
            ->getMock();
        /* @var $event InstalledPackage */
        $event = $get_event($package);
        $this->assertInstanceOf('\Symfony\Component\EventDispatcher\Event', $event);
        $this->assertEquals($package, $event->getPackage());
    }
}
