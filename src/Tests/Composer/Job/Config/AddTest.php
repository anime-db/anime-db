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

use AnimeDb\Bundle\AnimeDbBundle\Composer\Job\Config\Add;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Test job config add
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Tests\Composer\Job\Config
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class AddTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Root dir
     *
     * @var string
     */
    protected $root_dir;

    /**
     * Filesystem
     *
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $fs;

    /**
     * Construct
     *
     * @param string $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->fs = new Filesystem();
    }

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        parent::setUp();
        $this->root_dir = sys_get_temp_dir().'/tests/';
        $this->fs->mkdir($this->root_dir);

    }

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::tearDown()
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->fs->remove($this->root_dir);
    }

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
            'anime-db-bundle' => '\AnimeDb\Bundle\AppBundle\AnimeDbAppBundle',
            'anime-db-migrations' => ''
        ];

        return [
            [
                '/src/Resources/config/config.yml',
                "imports:\n    - { resource: '@AnimeDbAppBundle/Resources/config/config.yml' }\n",
                $extra
            ],
            [
                '/lib/Resources/config/global/config.xml',
                "imports:\n    - { resource: '@AnimeDbAppBundle/Resources/config/global/config.xml' }\n",
                $extra
            ],
            [
                '/undefined',
                '',
                $extra
            ],
            [
                '/undefined',
                "imports:\n    - { resource: '@AnimeDbAppBundle/Resources/config/my_config.yml' }\n",
                array_merge($extra, ['anime-db-config' => '/Resources/config/my_config.yml'])
            ]
        ];
    }

    /**
     * Test execute
     *
     * @dataProvider getPackageConfig
     *
     * @param string $package_config
     * @param string $expected
     * @param array $extra
     */
    public function testExecute($package_config, $expected, array $extra)
    {
        if (!empty($extra['anime-db-config'])) {
            $package_config = $extra['anime-db-config'];
        }
        $config = $this->root_dir.'app/config/vendor_config.yml';
        $package_config = $this->root_dir.'vendor/anime-db/app-bundle'.$package_config;
        $this->fs->mkdir([dirname($config), dirname($package_config)]);
        touch($config);
        touch($package_config);

        $package = $this->getMockBuilder('\Composer\Package\Package')
            ->disableOriginalConstructor()
            ->getMock();
        $package
            ->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn('anime-db/app-bundle');
        $package
            ->expects($this->atLeastOnce())
            ->method('getExtra')
            ->willReturn($extra);

        // test
        $job = new Add($package, $this->root_dir);
        $job->execute();

        $this->assertEquals($expected, file_get_contents($config));
    }
}