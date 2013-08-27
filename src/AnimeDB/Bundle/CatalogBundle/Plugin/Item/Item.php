<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\Bundle\CatalogBundle\Plugin\Item;

use AnimeDB\Bundle\CatalogBundle\Plugin\PluginInterface;
use AnimeDB\Bundle\CatalogBundle\Plugin\CustomMenu;

/**
 * Plugin item interface
 * 
 * @package AnimeDB\Bundle\CatalogBundle\Plugin\Item
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
interface Item extends PluginInterface, CustomMenu
{
}