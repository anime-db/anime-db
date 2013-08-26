<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\Bundle\CatalogBundle\Service\Plugin\Setting;

use AnimeDB\Bundle\CatalogBundle\Service\Plugin\PluginInterface;
use AnimeDB\Bundle\CatalogBundle\Service\Plugin\CustomController;

/**
 * Plugin setting interface
 * 
 * @package AnimeDB\Bundle\CatalogBundle\Service\Plugin\Setting
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
interface ImportInterface extends PluginInterface, CustomController
{
}