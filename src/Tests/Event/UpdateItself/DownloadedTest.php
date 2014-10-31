<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Tests\Event\UpdateItself;

use AnimeDb\Bundle\AnimeDbBundle\Event\UpdateItself\Downloaded;

/**
 * Test Downloaded event
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Tests\Event\UpdateItself
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class DownloadedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Path
     *
     * @var string
     */
    protected $path = 'foo';

    /**
     * New package
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $new_package;

    /**
     * Old package
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $old_package;

    /**
     * Event
     *
     * @var \AnimeDb\Bundle\AnimeDbBundle\Event\UpdateItself\Downloaded
     */
    protected $event;

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        parent::setUp();
        $this->new_package = $this->getMockBuilder('\Composer\Package\RootPackageInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->old_package = $this->getMockBuilder('\Composer\Package\RootPackageInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->event = new Downloaded($this->path, $this->new_package, $this->old_package);
    }

    /**
     * Test instance of event
     */
    public function testInstanceOfEvent()
    {
        $this->assertInstanceOf('\Symfony\Component\EventDispatcher\Event', $this->event);
    }

    /**
     * Test get path
     */
    public function testGetPath()
    {
        $this->assertEquals($this->path, $this->event->getPath());
    }

    /**
     * Test get new package
     */
    public function testGetNewPackage()
    {
        $this->assertEquals($this->new_package, $this->event->getNewPackage());
    }

    /**
     * Test get old package
     */
    public function testGetOldPackage()
    {
        $this->assertEquals($this->old_package, $this->event->getOldPackage());
    }
}
