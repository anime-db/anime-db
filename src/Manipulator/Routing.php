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

use Symfony\Component\Yaml\Yaml;

/**
 * Routing manipulator
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Manipulator
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Routing
{

    /**
     * Add a routing resource
     *
     * @param string $name
     * @param string $bundle
     * @param string $format
     * @param string $path
     *
     * @return Boolean true if it worked, false otherwise
     */
    public function addResource($name, $bundle, $format, $path = 'routing')
    {
        $file = __DIR__.'/../../app/config/routing.yml';
        $resource = '@'.$bundle.'/Resources/config/'.$path.'.'.$format;

        $value = Yaml::parse(file_get_contents($file));
        if (!isset($value[$name]) || $value[$name]['resource'] != $resource) {
            $value[$name] = ['resource' => $resource];
            file_put_contents($file, Yaml::dump($value, 2));
        }
    }

    /**
     * Remove a routing resource
     *
     * @param string $name
     */
    public function removeResource($name)
    {
        $file = __DIR__.'/../../app/config/routing.yml';
        $value = Yaml::parse(file_get_contents($file));
        if (isset($value[$name])) {
            unset($value[$name]);
            file_put_contents($file, Yaml::dump($value, 2));
        }
    }
}