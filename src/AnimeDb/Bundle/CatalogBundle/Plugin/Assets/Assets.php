<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Plugin\Assets;

use AnimeDb\Bundle\CatalogBundle\Plugin\Plugin;
use Knp\Menu\ItemInterface;

/**
 * Plugin for build assets
 * 
 * @package AnimeDb\Bundle\CatalogBundle\Plugin\Assets
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class Assets extends Plugin
{
    /**
     * Get links on js files
     *
     * @return array
     */
    abstract public function getJsPaths();

    /**
     * Get links on css files
     *
     * @return array
     */
    abstract public function getCssPaths();
}