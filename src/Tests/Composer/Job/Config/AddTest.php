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

    /**
     * Extra
     *
     * @var array
     */
    protected $extra = [
        'anime-db-routing' => '',
        'anime-db-config' => '',
        'anime-db-bundle' => '\AnimeDb\Bundle\AnimeDbBundle\AnimeDbAnimeDbBundle',
        'anime-db-migrations' => ''
    ];

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
     * Get package config
     *
     * @return array
     */
    public function getPackageConfig()
    {
        return [
            [
                $this->extra,
                '/Resources/config/config',
                'yml'
            ],
            [
                $this->extra,
                '/Resources/config/global/config',
                'xml'
            ],
            [
                array_merge($this->extra, ['anime-db-config' => '/Resources/config/my_config.yml']),
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
     * @param array $extra
     * @param string $path
     * @param string $ext
     */
    public function testExecute(array $extra, $path, $ext)
    {
        if (!empty($extra['anime-db-config'])) {
            $this->touchConfig($extra['anime-db-config']);
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
            ->willReturn($manipulator)
            ->with('config');

        // test
        $this->execute($extra);
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
        $this->execute($this->extra);
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
        $this->execute(array_merge($this->extra, ['anime-db-bundle' => '']));
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
     * @param array $extra
     */
    protected function execute(array $extra)
    {
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
            ->willReturn($extra);

        $job = new Add($package, $this->root_dir);
        $job->setContainer($this->container);
        $job->register();
        $job->execute();
    }
}