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
 * Parameters manipulator
 *
 * @package AnimeDb\Bundle\AnimeDbBundle\Manipulator
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Parameters extends Yaml
{
    /**
     * Get parameter
     *
     * @param string $key
     *
     * @return string
     */
    public function get($key)
    {
        $yaml = $this->getContent();
        return isset($yaml['parameters']) && isset($yaml['parameters'][$key]) ? $yaml['parameters'][$key] : '';
    }

    /**
     * Set parameter
     *
     * @param string $key
     * @param string $value
     */
    public function set($key, $value)
    {
        $yaml = $this->getContent();
        if (!isset($yaml['parameters'])) {
            $yaml['parameters'] = [];
        }
        $yaml['parameters'][$key] = $value;
        $this->setContent($yaml);
    }
}
