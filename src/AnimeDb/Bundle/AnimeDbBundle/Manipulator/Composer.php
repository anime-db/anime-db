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
        // TODO do add
    }

    /**
     * Remove the package from composer requirements
     *
     * @param string $bundle
     */
    public function removePackage($package)
    {
        // TODO do remove
    }
}