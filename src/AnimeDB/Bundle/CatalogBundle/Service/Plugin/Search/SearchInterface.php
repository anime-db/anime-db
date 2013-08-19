<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\Bundle\CatalogBundle\Service\Plugin\Search;

use AnimeDB\Bundle\CatalogBundle\Service\Plugin\PluginInterface;

/**
 * Plugin search interface
 * 
 * @package AnimeDB\Bundle\CatalogBundle\Service\Plugin\Search
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
interface SearchInterface extends PluginInterface
{
    /**
     * Search source by name
     *
     * Return structure
     * <code>
     * [
     *     \AnimeDB\Bundle\CatalogBundle\Service\Plugin\Search\Item
     * ]
     * </code>
     *
     * @param string $name
     *
     * @return array
     */
    public function search($name);
}