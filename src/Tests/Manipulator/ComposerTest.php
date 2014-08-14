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

use AnimeDb\Bundle\AnimeDbBundle\Manipulator\Composer;

/**
 * Test Composer Manipulator
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Tests\Manipulator
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ComposerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Manipulator
     *
     * @var \AnimeDb\Bundle\AnimeDbBundle\Manipulator\Composer
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
        $this->filename = tempnam(sys_get_temp_dir(), 'composer');
        $this->manipulator = new Composer($this->filename);
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
     * Get config for add package
     *
     * @return array
     */
    public function getConfigForAddPackage()
    {
        return [
            [
                [
                    'require' => [
                        'bar' => 'dev-master'
                    ]
                ],
                [
                    'require' => [
                        'bar' => 'dev-master',
                        'foo' => '1.0.0'
                    ]
                ]
            ],
            [
                [
                    'require' => [
                        'bar' => 'dev-master',
                        'foo' => '0.1.22'
                    ]
                ],
                [
                    'require' => [
                        'bar' => 'dev-master',
                        'foo' => '1.0.0'
                    ]
                ]
            ]
        ];
    }

    /**
     * Test add package
     *
     * @dataProvider getConfigForAddPackage
     *
     * @param array $before
     * @param array $after
     */
    public function testAddPackage(array $before, array $after)
    {
        file_put_contents($this->filename, json_encode($before, JSON_PRETTY_PRINT));

        $this->manipulator->addPackage('foo', '1.0.0'); // test

        $this->assertEquals(json_encode($after, JSON_PRETTY_PRINT), file_get_contents($this->filename));
    }

    /**
     * Get config for remove package
     *
     * @return array
     */
    public function getConfigForRemovePackage()
    {
        return [
            [
                [
                    'require' => [
                        'bar' => 'dev-master',
                        'foo' => '1.0.0'
                    ]
                ],
                [
                    'require' => [
                        'bar' => 'dev-master'
                    ]
                ]
            ],
            [
                [
                    'require' => [
                        'bar' => 'dev-master'
                    ]
                ],
                [
                    'require' => [
                        'bar' => 'dev-master'
                    ]
                ]
            ]
        ];
    }

    /**
     * Test remove package
     *
     * @dataProvider getConfigForRemovePackage
     *
     * @param array $before
     * @param array $after
     */
    public function testRemovePackage(array $before, array $after)
    {
        file_put_contents($this->filename, json_encode($before, JSON_PRETTY_PRINT));

        $this->manipulator->removePackage('foo'); // test

        $this->assertEquals(json_encode($after, JSON_PRETTY_PRINT), file_get_contents($this->filename));
    }
}