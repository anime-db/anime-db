<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\AnimeDbBundle\Manipulator;

class Routing extends Yaml
{
    /**
     * Add a routing resource.
     *
     * @param string $name
     * @param string $bundle
     * @param string $format
     * @param string $path
     */
    public function addResource($name, $bundle, $format, $path = 'routing')
    {
        $resource = '@'.$bundle.$path.'.'.$format;
        $yaml = $this->getContent();
        if (!isset($yaml[$name]) || $yaml[$name]['resource'] != $resource) {
            $yaml[$name] = ['resource' => $resource];
            $this->setContent($yaml);
        }
    }

    /**
     * Remove a routing resource.
     *
     * @param string $name
     */
    public function removeResource($name)
    {
        $yaml = $this->getContent();
        if (isset($yaml[$name])) {
            unset($yaml[$name]);
            $this->setContent($yaml);
        }
    }
}
