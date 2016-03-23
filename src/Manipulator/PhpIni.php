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
 * Class PhpIni
 * @package AnimeDb\Bundle\AnimeDbBundle\Manipulator
 */
class PhpIni implements ManipulatorInterface
{
    /**
     * @var string
     */
    protected $filename;

    /**
     * @var string[]
     */
    protected $ini = [];

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
     * @param string $key
     *
     * @return string
     */
    public function get($key)
    {
        $this->load();
        return isset($this->ini[$key]) ? $this->ini[$key] : '';
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $this->load();
        $this->ini[$key] = $value;
        $this->save();
    }

    protected function load()
    {
        if (!$this->ini) {
            $this->ini = (array)parse_ini_file($this->filename);
        }
    }

    protected function save()
    {
        if ($this->ini) {
            $content = '';
            foreach ($this->ini as $key => $value) {
                $content .= $key.'='.$value.PHP_EOL;
            }

            file_put_contents($this->filename, $content);
        }
    }
}
