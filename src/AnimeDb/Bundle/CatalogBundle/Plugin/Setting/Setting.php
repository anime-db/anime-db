<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Plugin\Setting;

use AnimeDb\Bundle\CatalogBundle\Plugin\Plugin;
use AnimeDb\Bundle\CatalogBundle\Plugin\CustomMenu;

/**
 * Plugin setting interface
 * 
 * @package AnimeDb\Bundle\CatalogBundle\Plugin\Setting
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
interface Setting extends Plugin, CustomMenu
{
}