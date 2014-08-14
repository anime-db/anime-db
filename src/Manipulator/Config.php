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
class Config extends Yaml
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
        $resource = '@'.$bundle.'/Resources/config/'.$path.'.'.$format;

        $yaml = $this->getContent();
        $yaml['imports'] = isset($yaml['imports']) ? $yaml['imports'] : [];
        // check for duplicate
        foreach ($yaml['imports'] as $import) {
            if ($import['resource'] == $resource) {
                return;
            }
        }
        $yaml['imports'][] = ['resource' => $resource];
        $this->setContent($yaml);
    }

    /**
     * Remove a routing resource
     *
     * @param string $bundle
     */
    public function removeResource($bundle)
    {
        $yaml = $this->getContent();
        if (!empty($yaml['imports'])) {
            foreach ($yaml['imports'] as $key => $import) {
                if (strpos($import['resource'], '@'.$bundle) === 0) {
                    unset($yaml['imports'][$key]);
                    $this->setContent($yaml);
                    break;
                }
            }
        }
    }
}