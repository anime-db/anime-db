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

use AnimeDb\Bundle\AnimeDbBundle\Manipulator\Kernel;

class KernelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Kernel
     */
    protected $manipulator;

    /**
     * @var string
     */
    protected $filename;

    protected function setUp()
    {
        $this->filename = tempnam(sys_get_temp_dir(), 'kernel');
        $kernel_filename = tempnam(sys_get_temp_dir(), 'kernel');
        $this->manipulator = new Kernel($this->filename, $kernel_filename);

        file_put_contents($kernel_filename, '
class AppKernel extends Kernel {
    public function registerBundles(){
        return [new AcmeBundle()];
    }
}');
    }

    protected function tearDown()
    {
        @unlink($this->filename);
    }

    public function testAddBundleAddedInKernel()
    {
        $this->manipulator->addBundle('\AcmeBundle'); // test

        $this->assertEmpty(file_get_contents($this->filename));
    }

    /**
     * @return array
     */
    public function getDataForAddBundle()
    {
        return [
            [
                '\DemoBundle',
                "<?php\nreturn [\n];",
                "<?php\nreturn [\n    new DemoBundle()\n];"
            ],
            [
                'DemoBundle',
                "<?php\nreturn [\n];",
                "<?php\nreturn [\n    new DemoBundle()\n];"
            ],
            [
                '\DemoBundle',
                "<?php\nreturn [\n    new DemoBundle()\n];",
                "<?php\nreturn [\n    new DemoBundle()\n];"
            ],
            [
                '\DemoBundle',
                "<?php\nreturn [\n    new FooBundle()\n];",
                "<?php\nreturn [\n    new FooBundle(),\n    new DemoBundle()\n];"
            ]
        ];
    }

    /**
     * @dataProvider getDataForAddBundle
     *
     * @param string $bundle
     * @param string $before
     * @param string $after
     */
    public function testAddBundle($bundle, $before, $after)
    {
        file_put_contents($this->filename, $before);

        $this->manipulator->addBundle($bundle); // test

        $this->assertEquals($after, file_get_contents($this->filename));
    }

    /**
     * @return array
     */
    public function getDataForRemoveBundle()
    {
        return [
            [
                '\DemoBundle',
                "<?php\nreturn [\n    new DemoBundle()\n];",
                "<?php\nreturn [\n];"
            ],
            [
                'DemoBundle',
                "<?php\nreturn [\n    new DemoBundle()\n];",
                "<?php\nreturn [\n];"
            ],
            [
                '\DemoBundle',
                "<?php\nreturn [\n    new FooBundle(),\n    new DemoBundle()\n];",
                "<?php\nreturn [\n    new FooBundle()\n];"
            ]
        ];
    }

    /**
     * @dataProvider getDataForRemoveBundle
     *
     * @param string $bundle
     * @param string $before
     * @param string $after
     */
    public function testRemoveBundle($bundle, $before, $after)
    {
        file_put_contents($this->filename, $before);

        $this->manipulator->removeBundle($bundle); // test

        $this->assertEquals($after, file_get_contents($this->filename));
    }
}
