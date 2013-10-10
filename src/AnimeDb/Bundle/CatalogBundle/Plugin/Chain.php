<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Plugin;

use AnimeDb\Bundle\CatalogBundle\Plugin\Plugin;

/**
 * Chain plugins
 * 
 * @package AnimeDb\Bundle\CatalogBundle\Plugin
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class Chain
{
    /**
     * List plugins
     *
     * @var array
     */
    protected $plugins = [];

    /**
     * List plugin titles
     *
     * @var array
     */
    protected $titles = [];

    /**
     * Add plugin
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Plugin\Plugin $plugin
     */
    public function addPlugin(Plugin $plugin) {
        $this->plugins[$plugin->getName()] = $plugin;
        $this->titles[$plugin->getName()] = $plugin->getTitle();
    }

    /**
     * Get plugin by name
     *
     * @param string $name
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Plugin\Plugin|null
     */
    public function getPlugin($name) {
        if (array_key_exists($name, $this->plugins)) {
            return $this->plugins[$name];
        }
        return null;
    }

    /**
     * Get plugins
     *
     * @return array [ \AnimeDb\Bundle\CatalogBundle\Plugin\Plugin ]
     */
    public function getPlugins() {
        return $this->plugins;
    }

    /**
     * Get plugin names
     *
     * @return array
     */
    public function getNames() {
        return array_keys($this->plugins);
    }

    /**
     * Get plugin titles
     *
     * @return array
     */
    public function getTitles() {
        return $this->titles;
    }
}