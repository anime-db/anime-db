<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\AnimeDbBundle\Tests\Event\UpdateItself;

use AnimeDb\Bundle\AnimeDbBundle\Event\UpdateItself\Downloaded;
use Composer\Package\RootPackageInterface;

class DownloadedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $path = 'foo';

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RootPackageInterface
     */
    protected $new_package;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RootPackageInterface
     */
    protected $old_package;

    /**
     * @var Downloaded
     */
    protected $event;

    protected function setUp()
    {
        $this->new_package = $this
            ->getMockBuilder('\Composer\Package\RootPackageInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->old_package = $this
            ->getMockBuilder('\Composer\Package\RootPackageInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->event = new Downloaded($this->path, $this->new_package, $this->old_package);
    }

    public function testInstanceOfEvent()
    {
        $this->assertInstanceOf('\Symfony\Component\EventDispatcher\Event', $this->event);
    }

    public function testGetPath()
    {
        $this->assertEquals($this->path, $this->event->getPath());
    }

    public function testGetNewPackage()
    {
        $this->assertEquals($this->new_package, $this->event->getNewPackage());
    }

    public function testGetOldPackage()
    {
        $this->assertEquals($this->old_package, $this->event->getOldPackage());
    }
}
