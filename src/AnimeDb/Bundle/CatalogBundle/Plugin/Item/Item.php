<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Plugin\Item;

use AnimeDb\Bundle\CatalogBundle\Plugin\Plugin;
use AnimeDb\Bundle\CatalogBundle\Plugin\CustomMenu;

/**
 * Plugin item interface
 * 
 * @package AnimeDb\Bundle\CatalogBundle\Plugin\Item
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
interface Item extends Plugin, CustomMenu
{
}