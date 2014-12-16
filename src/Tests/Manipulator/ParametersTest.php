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

use AnimeDb\Bundle\AnimeDbBundle\Manipulator\Parameters;
use Symfony\Component\Yaml\Yaml;

/**
 * Test parameters manipulator
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Tests\Manipulator
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ParametersTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Manipulator
     *
     * @var \AnimeDb\Bundle\AnimeDbBundle\Manipulator\Parameters
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
        $this->filename = tempnam(sys_get_temp_dir(), 'parameters.yml');
        $this->manipulator = new Parameters($this->filename);
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
     * Get data for set
     *
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
                        'foo' => 'bar'
                    ]
                ]
            ],
            [
                'foo',
                'bar',
                [
                    'parameters' => [
                        'baz' => 123,
                        'foo' => 'car'
                    ]
                ],
                [
                    'parameters' => [
                        'baz' => 123,
                        'foo' => 'bar'
                    ]
                ]
            ],
        ];
    }

    /**
     * Test set
     *
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
     * Get data for get
     *
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
                        'foo' => 'bar'
                    ]
                ]
            ],
            [
                'foo',
                '',
                [
                    'parameters' => [
                        'baz' => 123
                    ]
                ]
            ],
        ];
    }

    /**
     * Test get
     *
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
