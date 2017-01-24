<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Tests;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

abstract class TestCaseWritable extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $root_dir;

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @param string $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->fs = new Filesystem();
    }

    protected function setUp()
    {
        $this->root_dir = sys_get_temp_dir().'/tests/';
        $this->fs->mkdir($this->root_dir);
    }

    protected function tearDown()
    {
        $this->fs->remove(Finder::create()->in($this->root_dir)->directories()->files());
    }
}
