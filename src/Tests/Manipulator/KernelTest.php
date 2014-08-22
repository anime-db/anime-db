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

/**
 * Test Kernel Manipulator
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Tests\Manipulator
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class KernelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Manipulator
     *
     * @var \AnimeDb\Bundle\AnimeDbBundle\Manipulator\Kernel
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
     * Test add bundle added in kernel
     */
    public function testAddBundleAddedInKernel()
    {
        $this->manipulator->addBundle('\AcmeBundle'); // test

        $this->assertEmpty(file_get_contents($this->filename));
    }

    /**
     * Get data for add bundle
     *
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
     * Test add bundle
     *
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
     * Get data for remove bundle
     *
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
     * Test remove bundle
     *
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