<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\AnimeDbBundle\Tests\Manipulator;

use AnimeDb\Bundle\AnimeDbBundle\Manipulator\Routing;
use Symfony\Component\Yaml\Yaml;

class RoutingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Routing
     */
    protected $manipulator;

    /**
     * @var string
     */
    protected $filename;

    protected function setUp()
    {
        $this->filename = tempnam(sys_get_temp_dir(), 'routing');
        $this->manipulator = new Routing($this->filename);
    }

    protected function tearDown()
    {
        @unlink($this->filename);
    }

    /**
     * @return array
     */
    public function getDataForAddResource()
    {
        return [
            [
                'demo-bundle',
                'DemoBundle',
                'yml',
                '/Resources/config/routing',
                [],
                [
                    'demo-bundle' => [
                        'resource' => '@DemoBundle/Resources/config/routing.yml',
                    ],
                ],
            ],
            [
                'demo-bundle',
                'DemoBundle',
                'xml',
                '/Resources/config/global/my_routing',
                [
                    'acme-bundle' => [
                        'resource' => '@AcmeBundle/Resources/config/routing.yml',
                    ],
                ],
                [
                    'acme-bundle' => [
                        'resource' => '@AcmeBundle/Resources/config/routing.yml',
                    ],
                    'demo-bundle' => [
                        'resource' => '@DemoBundle/Resources/config/global/my_routing.xml',
                    ],
                ],
            ],
            [
                'demo-bundle',
                'DemoBundle',
                'yml',
                '/Resources/config/routing',
                [
                    'demo-bundle' => [
                        'resource' => '@DemoBundle/Resources/config/routing.yml',
                    ],
                ],
                [
                    'demo-bundle' => [
                        'resource' => '@DemoBundle/Resources/config/routing.yml',
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider getDataForAddResource
     *
     * @param string $name
     * @param string $bundle
     * @param string $format
     * @param string $path
     * @param array $before
     * @param array $after
     */
    public function testAddResource($name, $bundle, $format, $path, array $before, array $after)
    {
        file_put_contents($this->filename, Yaml::dump($before));

        $this->manipulator->addResource($name, $bundle, $format, $path); // test

        $this->assertEquals(Yaml::dump($after), file_get_contents($this->filename));
    }

    /**
     * @return array
     */
    public function getDataForRemoveResource()
    {
        return [
            [
                'demo-bundle',
                [],
                [],
            ],
            [
                'demo-bundle',
                [
                    'acme-bundle' => [
                        'resource' => '@AcmeBundle/Resources/config/routing.yml',
                    ],
                ],
                [
                    'acme-bundle' => [
                        'resource' => '@AcmeBundle/Resources/config/routing.yml',
                    ],
                ],
            ],
            [
                'demo-bundle',
                [
                    'acme-bundle' => [
                        'resource' => '@AcmeBundle/Resources/config/routing.yml',
                    ],
                    'demo-bundle' => [
                        'resource' => '@DemoBundle/Resources/config/routing.yml',
                    ],
                ],
                [
                    'acme-bundle' => [
                        'resource' => '@AcmeBundle/Resources/config/routing.yml',
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider getDataForRemoveResource
     *
     * @param string $name
     * @param array $before
     * @param array $after
     */
    public function testRemoveResource($name, array $before, array $after)
    {
        file_put_contents($this->filename, Yaml::dump($before));

        $this->manipulator->removeResource($name); // test

        $this->assertEquals(Yaml::dump($after), file_get_contents($this->filename));
    }
}
