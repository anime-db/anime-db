<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Tests;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Test case writable
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Tests
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class TestCaseWritable extends \PHPUnit_Framework_TestCase
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
}