<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AnimeDbBundle\Manipulator;

/**
 * File content manipulator
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Manipulator
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class FileContent implements Manipulator
{
    /**
     * Composer filename
     *
     * @var string
     */
    private $filename;

    /**
     * Construct
     *
     * @param string $filename
     */
    public function __construct($filename)
    {
        if (!file_exists($filename)) {
            throw new \RuntimeException("File '{$filename}' does not exist");
        }
        $this->filename = $filename;
    }

    /**
     * Get content
     *
     * @return string
     */
    protected function getContent()
    {
        return file_get_contents($this->filename);
    }

    /**
     * Set content
     *
     * @param string $content
     */
    protected function setContent($content)
    {
        file_put_contents($this->filename, $content);
    }
}