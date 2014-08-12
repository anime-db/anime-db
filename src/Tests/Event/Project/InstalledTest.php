<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Tests\Event\Project;

use AnimeDb\Bundle\AnimeDbBundle\Event\Project\Installed;

/**
 * Test Installed event
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Tests\Event\Project
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class InstalledTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test get package
     */
    public function testGetPackage()
    {
        $package = $this->getMockBuilder('\Composer\Package\Package')
            ->disableOriginalConstructor()
            ->getMock();
        $event = new Installed($package);
        $this->assertInstanceOf('\Symfony\Component\EventDispatcher\Event', $event);
        $this->assertEquals($package, $event->getPackage());
    }
}