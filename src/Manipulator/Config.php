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
class Config extends FileContent
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

        $value = $this->getContent();
        $value['imports'] = isset($value['imports']) ? $value['imports'] : [];
        // check for duplicate
        foreach ($value['imports'] as $import) {
            if ($import['resource'] == $resource) {
                return;
            }
        }
        $value['imports'][] = ['resource' => $resource];
        $this->setContent($value);
    }

    /**
     * Remove a routing resource
     *
     * @param string $bundle
     */
    public function removeResource($bundle)
    {
        $value = $this->getContent();
        if ($value['imports']) {
            foreach ($value['imports'] as $key => $import) {
                if (strpos($import['resource'], '@'.$bundle) === 0) {
                    unset($value['imports'][$key]);
                    $this->setContent($value);
                    break;
                }
            }
        }
    }

    /**
     * (non-PHPdoc)
     * @see \AnimeDb\Bundle\AnimeDbBundle\Manipulator\FileContent::getContent()
     */
    protected function getContent()
    {
        return Yaml::parse(parent::getContent());
    }

    /**
     * (non-PHPdoc)
     * @see \AnimeDb\Bundle\AnimeDbBundle\Manipulator\FileContent::setContent()
     */
    protected function setContent($content)
    {
        parent::setContent(Yaml::dump($content, 2));
    }
}