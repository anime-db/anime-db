<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\AnimeDbBundle\Manipulator;

class Parameters extends Yaml
{
    /**
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
     * @param string $key
     * @param mixed $value
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

    /**
     * @param string[] $parameters
     */
    public function setParameters(array $parameters)
    {
        if ($parameters) {
            $yaml = $this->getContent();
            if (!isset($yaml['parameters'])) {
                $yaml['parameters'] = [];
            }
            foreach ($parameters as $key => $value) {
                $yaml['parameters'][$key] = $value;
            }
            $this->setContent($yaml);
        }
    }
}
