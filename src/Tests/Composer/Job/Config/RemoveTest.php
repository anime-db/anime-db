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
     * Get package config
     *
     * @return array
     */
    public function getPackageConfig()
    {
        return [
            [
                [
                    ['resource' => '@AnimeDbAnimeDbBundle/Resources/config/config.yml']
                ],
                []
            ],
            [
                [
                    ['resource' => '@AnimeDbAnimeDbBundle/Resources/config/global/my_config.xml'],
                    ['resource' => '@AnimeDbAppBundle/Resources/config/config.yml']
                ],
                [
                    ['resource' => '@AnimeDbAppBundle/Resources/config/config.yml']
                ]
            ],
            [
                [
                    ['resource' => '@AnimeDbAppBundle/Resources/config/config.yml']
                ],
                [
                    ['resource' => '@AnimeDbAppBundle/Resources/config/config.yml']
                ]
            ]
        ];
    }

    /**
     * Test execute
     *
     * @dataProvider getPackageConfig
     *
     * @param array $default
     * @param array $expected
     */
    public function testExecute(array $default, array $expected)
    {
        $config = $this->root_dir.'app/config/vendor_config.yml';
        $this->fs->mkdir(dirname($config));
        file_put_contents($config, Yaml::dump(['imports' => $default]));

        $package = $this->getMockBuilder('\Composer\Package\Package')
            ->disableOriginalConstructor()
            ->getMock();
        $package
            ->expects($this->atLeastOnce())
            ->method('getExtra')
            ->willReturn([
                'anime-db-routing' => '',
                'anime-db-config' => '',
                'anime-db-bundle' => '\AnimeDb\Bundle\AnimeDbBundle\AnimeDbAnimeDbBundle',
                'anime-db-migrations' => ''
            ]);

        // test
        $job = new Remove($package, $this->root_dir);
        $job->setContainer($this->getMock('\AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Container'));
        $job->execute();

        $this->assertEquals(['imports' => $expected], Yaml::parse(file_get_contents($config)));
    }
}