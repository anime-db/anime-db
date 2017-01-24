<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Tests\Manipulator;

use AnimeDb\Bundle\AnimeDbBundle\Manipulator\Parameters;
use Symfony\Component\Yaml\Yaml;

class ParametersTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Parameters
     */
    protected $manipulator;

    /**
     * @var string
     */
    protected $filename;

    protected function setUp()
    {
        $this->filename = tempnam(sys_get_temp_dir(), 'parameters.yml');
        $this->manipulator = new Parameters($this->filename);
    }

    protected function tearDown()
    {
        @unlink($this->filename);
    }

    /**
     * @return array
     */
    public function getDataForSet()
    {
        return [
            [
                'foo',
                'bar',
                [],
                [
                    'parameters' => [
                        'foo' => 'bar',
                    ],
                ],
            ],
            [
                'foo',
                'bar',
                [
                    'parameters' => [
                        'baz' => 123,
                        'foo' => 'car',
                    ],
                ],
                [
                    'parameters' => [
                        'baz' => 123,
                        'foo' => 'bar',
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider getDataForSet
     *
     * @param string $key
     * @param string $value
     * @param array $before
     * @param array $after
     */
    public function testSet($key, $value, array $before, array $after)
    {
        file_put_contents($this->filename, Yaml::dump($before));

        $this->manipulator->set($key, $value);

        $this->assertEquals(Yaml::dump($after), file_get_contents($this->filename));
    }

    /**
     * @return array
     */
    public function getDataForSetParameters()
    {
        return [
            [
                ['foo' => 'bar'],
                [],
                [
                    'parameters' => [
                        'foo' => 'bar',
                    ],
                ],
            ],
            [
                [
                    'foo' => 'baz',
                    'bar' => true,
                ],
                [
                    'parameters' => [
                        'baz' => 123,
                        'foo' => 'car',
                    ],
                ],
                [
                    'parameters' => [
                        'baz' => 123,
                        'foo' => 'baz',
                        'bar' => true,
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider getDataForSetParameters
     *
     * @param array $parameters
     * @param array $before
     * @param array $after
     */
    public function testSetParameters(array $parameters, array $before, array $after)
    {
        file_put_contents($this->filename, Yaml::dump($before));

        $this->manipulator->setParameters($parameters);

        $this->assertEquals(Yaml::dump($after), file_get_contents($this->filename));
    }

    /**
     * @return array
     */
    public function getDataForGet()
    {
        return [
            [
                'foo',
                'bar',
                [
                    'parameters' => [
                        'foo' => 'bar',
                    ],
                ],
            ],
            [
                'foo',
                '',
                [
                    'parameters' => [
                        'baz' => 123,
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider getDataForGet
     *
     * @param string $key
     * @param string $value
     * @param array $before
     */
    public function testGet($key, $value, array $before)
    {
        file_put_contents($this->filename, Yaml::dump($before));

        $this->assertEquals($value, $this->manipulator->get($key));
    }
}
