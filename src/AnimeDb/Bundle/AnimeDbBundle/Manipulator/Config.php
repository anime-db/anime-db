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
        $file = __DIR__.'/../../../../../app/config/vendor_config.yml';
        $resource = '@'.$bundle.'/Resources/config/'.$path.'.'.$format;

        $value = Yaml::parse(file_get_contents($file));
        // check for duplicate
        foreach ($value['imports'] as $import) {
            if ($import['resource'] == $resource) {
                return;
            }
        }
        $value['imports'][] = ['resource' => $resource];
        file_put_contents($file, Yaml::dump($value, 2));
    }

    /**
     * Remove a routing resource
     *
     * @param string $bundle
     */
    public function removeResource($bundle)
    {
        $file = __DIR__.'/../../../../../app/config/vendor_config.yml';
        $value = Yaml::parse(file_get_contents($file));
        foreach ($value['imports'] as $key => $import) {
            if (strpos($import['resource'], '@'.$bundle) === 0) {
                unset($value['imports'][$key]);
                file_put_contents($file, Yaml::dump($value, 2));
            }
        }
    }
}