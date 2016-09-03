<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\AnimeDbBundle\Tests\Manipulator;

use AnimeDb\Bundle\AnimeDbBundle\Manipulator\Composer;

class ComposerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Composer
     */
    protected $manipulator;

    /**
     * @var string
     */
    protected $filename;

    protected function setUp()
    {
        $this->filename = tempnam(sys_get_temp_dir(), 'composer');
        $this->manipulator = new Composer($this->filename);
    }

    protected function tearDown()
    {
        @unlink($this->filename);
    }

    /**
     * @return array
     */
    public function getConfigForAddPackage()
    {
        return [
            [
                [
                    'require' => [
                        'bar' => 'dev-master',
                    ],
                ],
                [
                    'require' => [
                        'bar' => 'dev-master',
                        'foo' => '1.0.0',
                    ],
                ],
            ],
            [
                [
                    'require' => [
                        'bar' => 'dev-master',
                        'foo' => '0.1.22',
                    ],
                ],
                [
                    'require' => [
                        'bar' => 'dev-master',
                        'foo' => '1.0.0',
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider getConfigForAddPackage
     *
     * @param array $before
     * @param array $after
     */
    public function testAddPackage(array $before, array $after)
    {
        file_put_contents($this->filename, $this->encode($before));

        $this->manipulator->addPackage('foo', '1.0.0'); // test

        $this->assertEquals($this->encode($after), file_get_contents($this->filename));
    }

    /**
     * @return array
     */
    public function getConfigForRemovePackage()
    {
        return [
            [
                [
                    'require' => [
                        'bar' => 'dev-master',
                        'foo' => '1.0.0',
                    ],
                ],
                [
                    'require' => [
                        'bar' => 'dev-master',
                    ],
                ],
            ],
            [
                [
                    'require' => [
                        'bar' => 'dev-master',
                    ],
                ],
                [
                    'require' => [
                        'bar' => 'dev-master',
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider getConfigForRemovePackage
     *
     * @param array $before
     * @param array $after
     */
    public function testRemovePackage(array $before, array $after)
    {
        file_put_contents($this->filename, $this->encode($before));

        $this->manipulator->removePackage('foo'); // test

        $this->assertEquals($this->encode($after), file_get_contents($this->filename));
    }

    /**
     * Encode dat to JSON.
     *
     * @param array $data
     *
     * @return string
     */
    protected function encode(array $data)
    {
        $content = json_encode($data, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return str_replace(['": ', '    '], ['" : ', "\t"], $content).PHP_EOL;
    }
}
