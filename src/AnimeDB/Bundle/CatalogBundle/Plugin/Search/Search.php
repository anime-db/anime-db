<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\Bundle\CatalogBundle\Plugin\Search;

use AnimeDB\Bundle\CatalogBundle\Plugin\Plugin;

/**
 * Plugin search interface
 * 
 * @package AnimeDB\Bundle\CatalogBundle\Plugin\Search
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
interface Search extends Plugin
{
    /**
     * Search source by name
     *
     * Use $url_bulder for build link to fill item from source or build their own links
     *
     * Return structure
     * <code>
     * [
     *     \AnimeDB\Bundle\CatalogBundle\Plugin\Search\Item
     * ]
     * </code>
     *
     * @param string $name
     * @param \Closure $url_bulder
     *
     * @return array
     */
    public function search($name, \Closure $url_bulder);
}