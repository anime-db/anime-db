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

use AnimeDb\Bundle\AnimeDbBundle\Event\UpdateItself\Updated;

/**
 * Test Updated event
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Tests\Event\UpdateItself
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class UpdatedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test get package
     */
    public function testGetPackage()
    {
        $package = $this->getMockBuilder('\Composer\Package\Package')
            ->disableOriginalConstructor()
            ->getMock();
        $event = new Updated($package);
        $this->assertInstanceOf('\Symfony\Component\EventDispatcher\Event', $event);
        $this->assertEquals($package, $event->getPackage());
    }
}