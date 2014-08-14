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
 * Composer manipulator
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Manipulator
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Composer
{
    /**
     * Composer filename
     *
     * @var string
     */
    protected $filename;

    /**
     * Construct
     *
     * @param string $filename
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Add the package into composer requirements
     *
     * @param string $package
     * @param string $version
     */
    public function addPackage($package, $version)
    {
        $config = $this->getConfig();
        $config['require'][$package] = $version;
        $this->setConfig($config);
    }

    /**
     * Remove the package from composer requirements
     *
     * @param string $bundle
     */
    public function removePackage($package)
    {
        $config = $this->getConfig();
        if (isset($config['require'][$package])) {
            unset($config['require'][$package]);
            $this->setConfig($config);
        }
    }

    /**
     * Get config
     *
     * @return array
     */
    protected function getConfig()
    {
        return (array)json_decode(file_get_contents($this->filename), true);
    }

    /**
     * Set config
     *
     * @param array $config
     */
    protected function setConfig(array $config)
    {
        $config = json_encode($config, JSON_NUMERIC_CHECK|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
        file_put_contents($this->filename, $config);
    }
}