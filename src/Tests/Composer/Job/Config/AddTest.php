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
     * Get package config
     *
     * @return array
     */
    public function getPackageConfig()
    {
        $extra = [
            'anime-db-routing' => '',
            'anime-db-config' => '',
            'anime-db-bundle' => '\AnimeDb\Bundle\AnimeDbBundle\AnimeDbAnimeDbBundle',
            'anime-db-migrations' => ''
        ];

        return [
            [
                $extra,
                '/Resources/config/config',
                'yml'
            ],
            [
                $extra,
                '/Resources/config/global/config',
                'xml'
            ],/* 
            [
                $extra,
                '',
                ''
            ], */
            [
                array_merge($extra, ['anime-db-config' => '/Resources/config/my_config.yml']),
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
            $package_config = $extra['anime-db-config'];
        } else {
            $package_config = '/src'.$path.'.'.$ext;
        }
        $package_config = $this->root_dir.'vendor/anime-db/anime-db'.$package_config;
        $this->fs->mkdir(dirname($package_config));
        touch($package_config);

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
        $manipulator = $this->getMockBuilder('\AnimeDb\Bundle\AnimeDbBundle\Manipulator\Config')
            ->disableOriginalConstructor()
            ->getMock();
        $manipulator
            ->expects($this->once())
            ->method('addResource')
            ->with('AnimeDbAnimeDbBundle', $ext, $path);
        $container = $this->getMock('\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Container');
        $container
            ->expects($this->once())
            ->method('getManipulator')
            ->willReturn($manipulator)
            ->with('config');

        // test
        $job = new Add($package, $this->root_dir);
        $job->setContainer($container);
        $job->register();
        $job->execute();
    }
}