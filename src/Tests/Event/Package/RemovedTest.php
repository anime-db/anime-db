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

use AnimeDb\Bundle\AnimeDbBundle\Event\Package\Removed;

/**
 * Test Removed event
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Tests\Event\Package
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class RemovedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test get package
     */
    public function testGetPackage()
    {
        $package = $this->getMockBuilder('\Composer\Package\Package')
            ->disableOriginalConstructor()
            ->getMock();
        $event = new Removed($package);
        $this->assertInstanceOf('\Symfony\Component\EventDispatcher\Event', $event);
        $this->assertEquals($package, $event->getPackage());
    }
}