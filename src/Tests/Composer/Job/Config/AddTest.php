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
                array_merge($this->extra, ['anime-db-config' => '/my_dir/Resources/config/my_config.yml']),
                '/my_dir/Resources/config/my_config',
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
        $job = new Add($this->getPackage($extra), $this->root_dir);
        $job->setContainer($this->container);
        $job->register();
        $job->execute();
    }

    /**
     * Get package bad config
     *
     * @return array
     */
    public function getPackageBadConfig()
    {
        return [
            [array_merge($this->extra, ['anime-db-bundle' => ''])],
            [$this->extra]
        ];
    }

    /**
     * Test execute not add
     *
     * @dataProvider getPackageBadConfig
     *
     * @param array $extra
     */
    public function testExecuteNotAdd(array $extra)
    {
        $this->touchConfig('/undefined');
        $this->container
            ->expects($this->never())
            ->method('getManipulator');

        // test
        $job = new Add($this->getPackage($extra), $this->root_dir);
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
        $filename = $this->root_dir.'vendor/anime-db/anime-db'.$filename;
        $this->fs->mkdir(dirname($filename));
        touch($filename);
    }

    /**
     * Get package
     *
     * @param array $extra
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getPackage(array $extra)
    {
        $package = $this->getMockBuilder('\Composer\Package\Package')
            ->disableOriginalConstructor()
            ->getMock();
        $package
            ->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn('anime-db/anime-db');
        $package
            ->expects($this->atLeastOnce())
            ->method('getExtra')
            ->willReturn($extra);
        return $package;
    }
}