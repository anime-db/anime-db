<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Plugin\Search;

use AnimeDb\Bundle\CatalogBundle\Plugin\Plugin;
use Knp\Menu\ItemInterface;

/**
 * Plugin search
 * 
 * @package AnimeDb\Bundle\CatalogBundle\Plugin\Search
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class Search extends Plugin
{
    /**
     * Search source by name
     *
     * Use $url_bulder for build link to fill item from source or build their own links
     *
     * Return structure
     * <code>
     * [
     *     \AnimeDb\Bundle\CatalogBundle\Plugin\Search\Item
     * ]
     * </code>
     *
     * @param string $name
     * @param \Closure $url_bulder
     *
     * @return array
     */
    abstract public function search($name, \Closure $url_bulder);

    /**
     * Build menu for plugin
     *
     * @param \Knp\Menu\ItemInterface $item
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function buildMenu(ItemInterface $item)
    {
        $item->addChild($this->getTitle(), [
            'route' => 'item_search',
            'routeParameters' => ['plugin' => $this->getName()]
        ]);
    }
}