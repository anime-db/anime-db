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

abstract class FileContent implements ManipulatorInterface
{
    /**
     * @var string
     */
    private $filename;

    /**
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
     * @return string
     */
    protected function getContent()
    {
        return file_get_contents($this->filename);
    }

    /**
     * @param string $content
     */
    protected function setContent($content)
    {
        file_put_contents($this->filename, $content);
    }
}
