<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\AnimeDbBundle\Manipulator;

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
     * @return string|array
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
            $ini = file_get_contents($this->filename);
            $ini = str_replace(["\r\n", "\r"], "\n", $ini);
            $ini = explode("\n", $ini);

            foreach ($ini as $row) {
                if ($row && ($row = parse_ini_string($row, false, INI_SCANNER_RAW))) {
                    foreach ($row as $key => $value) {
                        if (!isset($this->ini[$key])) {
                            $this->ini[$key] = $value;
                        } elseif (!is_array($this->ini[$key])) {
                            $this->ini[$key] = [$this->ini[$key], $value];
                        } else {
                            $this->ini[$key][] = $value;
                        }
                    }
                }
            }
        }
    }

    protected function save()
    {
        if ($this->ini) {
            $content = '';
            foreach ($this->ini as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $v) {
                        $content .= $key.' = '.$v.PHP_EOL;
                    }
                } else {
                    $content .= $key.' = '.$value.PHP_EOL;
                }
            }

            file_put_contents($this->filename, $content);
        }
    }

    /**
     * @param string $byte
     *
     * @return int
     */
    public function byteStringToInt($byte)
    {
        switch (strtoupper(substr($byte, -1, 1))) {
            case 'K':
                $int = substr($byte, 0, -1) * 1024;
                break;
            case 'M':
                $int = substr($byte, 0, -1) * 1048576; // 1024 * 1024
                break;
            case 'G':
                $int = substr($byte, 0, -1) * 1073741824; // 1024 * 1024 * 1024
                break;
            default:
                $int = $byte;
        }

        return (int) $int;
    }

    /**
     * @param int $int
     *
     * @return string
     */
    public function byteIntToString($int)
    {
        if ($int % 1073741824 == 0) { // 1024 * 1024 * 1024
            return ($int / 1073741824).'G';
        } elseif ($int % 1048576 == 0) { // 1024 * 1024
            return ($int / 1048576).'M';
        } elseif ($int % 1024 == 0) {
            return ($int / 1024).'K';
        }

        return (string) $int;
    }
}
