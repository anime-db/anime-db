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

class Composer extends FileContent
{
    /**
     * Add the package into composer requirements
     *
     * @param string $package
     * @param string $version
     */
    public function addPackage($package, $version)
    {
        $config = $this->getContent();
        $config['require'][$package] = $version;
        $this->setContent($config);
    }

    /**
     * Remove the package from composer requirements
     *
     * @param string $package
     */
    public function removePackage($package)
    {
        $config = $this->getContent();
        if (isset($config['require'][$package])) {
            unset($config['require'][$package]);
            $this->setContent($config);
        }
    }

    /**
     * @return array
     */
    protected function getContent()
    {
        return (array)json_decode(parent::getContent(), true);
    }

    /**
     * @param string $content
     */
    protected function setContent($content)
    {
        $content = json_encode($content, JSON_NUMERIC_CHECK|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
        $content = str_replace(['": ', '    '], ['" : ', "\t"], $content).PHP_EOL;
        parent::setContent($content);
    }
}
