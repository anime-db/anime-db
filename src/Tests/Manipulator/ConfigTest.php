<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Tests\Manipulator;

use AnimeDb\Bundle\AnimeDbBundle\Manipulator\Config;
use Symfony\Component\Yaml\Yaml;

/**
 * Test Config Manipulator
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Tests\Manipulator
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Manipulator
     *
     * @var \AnimeDb\Bundle\AnimeDbBundle\Manipulator\Config
     */
    protected $manipulator;

    /**
     * Filename
     *
     * @var string
     */
    protected $filename;

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        parent::setUp();
        $this->filename = tempnam(sys_get_temp_dir(), 'config');
        $this->manipulator = new Config($this->filename);
    }

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::tearDown()
     */
    protected function tearDown()
    {
        parent::tearDown();
        @unlink($this->filename);
    }

    /**
     * Get data for add resource
     *
     * @return array
     */
    public function getDataForAddResource()
    {
        return [
            [
                'DemoBundle',
                'yml',
                '/Resources/config/config',
                [],
                [
                    'imports' => [
                        [
                            'resource' => '@DemoBundle/Resources/config/config.yml'
                        ]
                    ]
                ]
            ],
            [
                'DemoBundle',
                'xml',
                '/Resources/config/my_config',
                [
                    'imports' => [
                        [
                            'resource' => '@AcmeBundle/Resources/config/config.xml'
                        ]
                    ]
                ],
                [
                    'imports' => [
                        [
                            'resource' => '@AcmeBundle/Resources/config/config.xml'
                        ],
                        [
                            'resource' => '@DemoBundle/Resources/config/my_config.xml'
                        ]
                    ]
                ]
            ],
            [
                'DemoBundle',
                'yml',
                '/Resources/config/config',
                [
                    'imports' => [
                        [
                            'resource' => '@DemoBundle/Resources/config/config.yml'
                        ]
                    ]
                ],
                [
                    'imports' => [
                        [
                            'resource' => '@DemoBundle/Resources/config/config.yml'
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Test add resource
     *
     * @dataProvider getDataForAddResource
     *
     * @param string $bundle
     * @param string $format
     * @param string $path
     * @param array $before
     * @param array $after
     */
    public function testAddResource($bundle, $format, $path, array $before, array $after)
    {
        file_put_contents($this->filename, Yaml::dump($before));

        $this->manipulator->addResource($bundle, $format, $path); // test

        $this->assertEquals(Yaml::dump($after), file_get_contents($this->filename));
    }

    /**
     * Get data for remove resource
     *
     * @return array
     */
    public function getDataForRemoveResource()
    {
        return [
            [
                'DemoBundle',
                [],
                []
            ],
            [
                'DemoBundle',
                [
                    ['resource' => '@AcmeBundle/Resources/config/config.xml']
                ],
                [
                    ['resource' => '@AcmeBundle/Resources/config/config.xml']
                ]
            ],
            [
                'DemoBundle',
                [
                    ['resource' => '@DemoBundle/Resources/config/my_config.xml'],
                    ['resource' => '@AcmeBundle/Resources/config/config.xml']
                ],
                [
                    ['resource' => '@AcmeBundle/Resources/config/config.xml']
                ]
            ]
        ];
    }

    /**
     * Test add resource
     *
     * @dataProvider getDataForRemoveResource
     *
     * @param string $bundle
     * @param array $before
     * @param array $after
     */
    public function testRemoveResource($bundle, array $before, array $after)
    {
        file_put_contents($this->filename, Yaml::dump(['imports' => $before]));

        $this->manipulator->removeResource($bundle); // test

        $this->assertEquals(['imports' => $after], Yaml::parse(file_get_contents($this->filename)));
    }
}