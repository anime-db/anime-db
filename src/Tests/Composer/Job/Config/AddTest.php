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
use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Config\Add;

/**
 * Test job config add
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Tests\Composer\Job\Config
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

    protected function setUp()
    {
        parent::setUp();
        $this->container = $this->getMockBuilder('\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Container')
            ->disableOriginalConstructor()
            ->getMock();
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
                '/Resources/config/config',
                'yml'
            ],
            [
                '',
                '/Resources/config/global/config',
                'xml'
            ],
            [
                '/Resources/config/my_config.yml',
                '/Resources/config/my_config',
                'yml'
            ]
        ];
    }

    /**
     * Test execute
     *
     * @dataProvider getPackageConfig
     *
     * @param string $config
     * @param string $path
     * @param string $ext
     */
    public function testExecute($config, $path, $ext)
    {
        if ($config) {
            $this->touchConfig($config);
        } else {
            $this->touchConfig('/src'.$path.'.'.$ext);
        }
        $manipulator = $this->getMockBuilder('\AnimeDb\Bundle\AnimeDbBundle\Manipulator\Config')
            ->disableOriginalConstructor()
            ->getMock();
        $manipulator
            ->expects($this->once())
            ->method('addResource')
            ->with('AnimeDbAnimeDbBundle', $ext, $path);
        $this->container
            ->expects($this->once())
            ->method('getManipulator')
            ->will($this->returnValue($manipulator))
            ->with('config');

        // test
        $this->execute($config);
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
        $this->touchConfig('/src/Resources/config/config.yml');
        $this->container
            ->expects($this->never())
            ->method('getManipulator');

        // test
        $this->execute('', '');
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
     * @param string $config
     * @param string $bundle
     */
    protected function execute(
        $config = '',
        $bundle = '\AnimeDb\Bundle\AnimeDbBundle\AnimeDbAnimeDbBundle'
    ) {
        $package = $this->getMockBuilder('\Composer\Package\Package')
            ->disableOriginalConstructor()
            ->getMock();
        $package
            ->expects($this->atLeastOnce())
            ->method('getName')
            ->will($this->returnValue('foo/bar'));
        $package
            ->expects($this->atLeastOnce())
            ->method('getExtra')
            ->will($this->returnValue([
                'anime-db-routing' => '',
                'anime-db-config' => $config,
                'anime-db-bundle' => $bundle,
                'anime-db-migrations' => ''
            ]));

        $job = new Add($package);
        $job->setContainer($this->container);
        $job->setRootDir($this->root_dir);
        $job->execute();
    }
}
