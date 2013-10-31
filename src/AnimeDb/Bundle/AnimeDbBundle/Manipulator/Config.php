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
 * Config manipulator
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Manipulator
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Config
{
    /**
     * Add a routing resource
     *
     * @param string $bundle
     * @param string $format
     * @param string $path
     */
    public function addResource($bundle, $format, $path = 'config')
    {
        // TODO do add
    }

    /**
     * Remove a routing resource
     *
     * @param string $bundle
     */
    public function removeResource($bundle)
    {
        // TODO do remove
    }
}