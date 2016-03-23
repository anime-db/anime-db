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

/**
 * Test File content manipulator
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Tests\Manipulator
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class FileContentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \RuntimeException
     */
    public function testEmptyFile()
    {
        $this->getMockForAbstractClass('\AnimeDb\Bundle\AnimeDbBundle\Manipulator\FileContent', ['']);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testFileNotExists()
    {
        $this->getMockForAbstractClass('\AnimeDb\Bundle\AnimeDbBundle\Manipulator\FileContent', [sys_get_temp_dir().'/no-file']);
    }
}
