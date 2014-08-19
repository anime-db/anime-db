<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Tests\Composer\Job\Routing;

use AnimeDb\Bundle\AnimeDbBundle\Tests\TestCaseWritable;
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Routing\Add;

/**
 * Test job routing add
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Tests\Composer\Job\Routing
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class AddTest extends TestCaseWritable
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
    protected function setUp()
    {
        parent::setUp();
        $this->container = $this->getMock('\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Container');
    }

    /**
     * Get package config
     *
     * @return array
     */
    public function getPackageConfig()
    {
        return [
            [
                '',
                '/Resources/config/routing',
                'yml'
            ],
            [
                '',
                '/Resources/config/global/routing',
                'xml'
            ],
            [
                '/Resources/config/my_routing.yml',
                '/Resources/config/my_routing',
                'yml'
            ]
        ];
    }

    /**
     * Test execute
     *
     * @dataProvider getPackageConfig
     *
     * @param string $routing
     * @param string $path
     * @param string $ext
     */
    public function testExecute($routing, $path, $ext)
    {
        if ($routing) {
            $this->touchConfig($routing);
        } else {
            $this->touchConfig('/src'.$path.'.'.$ext);
        }
        $manipulator = $this->getMockBuilder('\AnimeDb\Bundle\AnimeDbBundle\Manipulator\Routing')
            ->disableOriginalConstructor()
            ->getMock();
        $manipulator
            ->expects($this->once())
            ->method('addResource')
            ->with('foo_bar', 'AnimeDbAnimeDbBundle', $ext, $path);
        $this->container
            ->expects($this->once())
            ->method('getManipulator')
            ->willReturn($manipulator)
            ->with('routing');

        // test
        $this->execute($routing);
    }

    /**
     * Test execute no config
     */
    public function testExecuteNoConfig()
    {
        $this->touchConfig('/undefined');
        $this->container
            ->expects($this->never())
            ->method('getManipulator');

        // test
        $this->execute();
    }

    /**
     * Test execute failed. Undefined bundle
     */
    public function testExecuteNoBundle()
    {
        $this->touchConfig('/src/Resources/config/routing.yml');
        $this->container
            ->expects($this->never())
            ->method('getManipulator');

        // test
        $this->execute('', '');
    }

    /**
     * Test execute failed. Ignore bundle
     */
    public function testExecuteIgnoreBundle()
    {
        $package = $this->getMockBuilder('\Composer\Package\Package')
            ->disableOriginalConstructor()
            ->getMock();
        $package
            ->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn('sensio/framework-extra-bundle');
        $package
            ->expects($this->atLeastOnce())
            ->method('getExtra')
            ->willReturn([
                'anime-db-routing' => '',
                'anime-db-config' => '',
                'anime-db-bundle' => '\AnimeDb\Bundle\AnimeDbBundle\AnimeDbAnimeDbBundle',
                'anime-db-migrations' => ''
            ]);

        $job = new Add($package, $this->root_dir);
        $job->setContainer($this->container);
        $job->register();
        $job->execute();
    }

    /**
     * Touch config
     *
     * @param string $filename
     */
    protected function touchConfig($filename)
    {
        $filename = $this->root_dir.'vendor/foo/bar'.$filename;
        $this->fs->mkdir(dirname($filename));
        touch($filename);
    }

    /**
     * Execute job
     *
     * @param string $routing
     * @param string $bundle
     */
    protected function execute(
        $routing = '',
        $bundle = '\AnimeDb\Bundle\AnimeDbBundle\AnimeDbAnimeDbBundle'
    ) {
        $package = $this->getMockBuilder('\Composer\Package\Package')
            ->disableOriginalConstructor()
            ->getMock();
        $package
            ->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn('foo/bar');
        $package
            ->expects($this->atLeastOnce())
            ->method('getExtra')
            ->willReturn([
                'anime-db-routing' => $routing,
                'anime-db-config' => '',
                'anime-db-bundle' => $bundle,
                'anime-db-migrations' => ''
            ]);

        $job = new Add($package, $this->root_dir);
        $job->setContainer($this->container);
        $job->register();
        $job->execute();
    }
}