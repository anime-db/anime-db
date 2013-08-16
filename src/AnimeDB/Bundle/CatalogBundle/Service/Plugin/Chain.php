<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\Bundle\CatalogBundle\Service\Plugin;

use AnimeDB\Bundle\CatalogBundle\Service\Plugin\PluginInterface;

/**
 * Chain plugins
 * 
 * @package AnimeDB\Bundle\CatalogBundle\Service\Plugin
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
     * @param \AnimeDB\Bundle\CatalogBundle\Service\Plugin\PluginInterface $plugin
     */
    public function addPlugin(PluginInterface $plugin) {
        $this->plugins[$plugin->getName()] = $plugin;
        $this->titles[$plugin->getName()] = $plugin->getTitle();
    }

    /**
     * Get plugin by name
     *
     * @param string $name
     *
     * @return \AnimeDB\Bundle\CatalogBundle\Service\Plugin\PluginInterface|null
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
     * @return array [ \AnimeDB\Bundle\CatalogBundle\Service\Plugin\PluginInterface ]
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