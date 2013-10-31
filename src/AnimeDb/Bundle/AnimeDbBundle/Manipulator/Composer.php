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
     * Add the package into composer requirements
     *
     * @param string $package
     * @param string $version
     */
    public function addPackage($package, $version)
    {
        $composer = file_get_contents(__DIR__.'/../../../../../composer.json');
        $composer = json_decode($composer, true);
        $composer['require'][$package] = $version;
        $composer = json_encode($composer, JSON_NUMERIC_CHECK|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
        file_put_contents(__DIR__.'/../../../../../composer.json', $composer);
    }

    /**
     * Remove the package from composer requirements
     *
     * @param string $bundle
     */
    public function removePackage($package)
    {
        $composer = file_get_contents(__DIR__.'/../../../../../composer.json');
        $composer = json_decode($composer, true);
        if (isset($composer['require'][$package])) {
            unset($composer['require'][$package]);
            $composer = json_encode($composer, JSON_NUMERIC_CHECK|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
            file_put_contents(__DIR__.'/../../../../../composer.json', $composer);
        }
    }
}