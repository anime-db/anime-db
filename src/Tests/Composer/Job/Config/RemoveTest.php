<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Tests\Composer\Job\Config;

use AnimeDb\Bundle\AnimeDbBundle\Tests\TestCaseWritable;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Config\Remove;
use Symfony\Component\Yaml\Yaml;

/**
 * Test job config remove
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Tests\Composer\Job\Config
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class RemoveTest extends TestCaseWritable
{
    /**
     * Container
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $container;

    /**
     * (non-PHPdoc)
     * @see \AnimeDb\Bundle\AnimeDbBundle\Tests\TestCaseWritable::setUp()
     */
    public function setUp()
    {
        parent::setUp();
        $this->container = $this->getMock('\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Container');
    }

    /**
     * Test success add in execute
     */
    public function testSuccessAdd()
    {
        $manipulator = $this->getMockBuilder('\AnimeDb\Bundle\AnimeDbBundle\Manipulator\Config')
            ->disableOriginalConstructor()
            ->getMock();
        $manipulator
            ->expects($this->once())
            ->method('removeResource')
            ->with('AnimeDbAnimeDbBundle');
        $this->container
            ->expects($this->once())
            ->method('getManipulator')
            ->willReturn($manipulator)
            ->with('config');

        // test
        $this->execute([
            'anime-db-routing' => '',
            'anime-db-config' => '',
            'anime-db-bundle' => '\AnimeDb\Bundle\AnimeDbBundle\AnimeDbAnimeDbBundle',
            'anime-db-migrations' => ''
        ]);
    }

    /**
     * Test no add in execute
     */
    public function testNoAdd()
    {
        $this->container
            ->expects($this->never())
            ->method('getManipulator');

        // test
        $this->execute([
            'anime-db-routing' => '',
            'anime-db-config' => '',
            'anime-db-bundle' => '',
            'anime-db-migrations' => ''
        ]);
    }

    /**
     * Execute job
     *
     * @param array $extra
     */
    protected function execute(array $extra)
    {
        $package = $this->getMockBuilder('\Composer\Package\Package')
            ->disableOriginalConstructor()
            ->getMock();
        $package
            ->expects($this->any())
            ->method('getName')
            ->willReturn('foo/bar');
        $package
            ->expects($this->atLeastOnce())
            ->method('getExtra')
            ->willReturn($extra);

        $job = new Remove($package, $this->root_dir);
        $job->setContainer($this->container);
        $job->register();
        $job->execute();
    }
}