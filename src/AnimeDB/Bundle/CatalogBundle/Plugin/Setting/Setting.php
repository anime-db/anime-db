<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\Bundle\CatalogBundle\Plugin\Setting;

use AnimeDB\Bundle\CatalogBundle\Plugin\PluginInterface;
use AnimeDB\Bundle\CatalogBundle\Plugin\CustomMenu;

/**
 * Plugin setting interface
 * 
 * @package AnimeDB\Bundle\CatalogBundle\Plugin\Setting
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
interface Setting extends PluginInterface, CustomMenu
{
}