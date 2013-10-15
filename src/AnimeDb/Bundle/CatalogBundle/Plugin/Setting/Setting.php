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

/**
 * Plugin setting interface
 * 
 * @package AnimeDb\Bundle\CatalogBundle\Plugin\Setting
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class Setting extends Plugin
{
    /**
     * Build menu for plugin
     *
     * @param \Knp\Menu\ItemInterface $item
     *
     * @return \Knp\Menu\ItemInterface
     */
    abstract public function buildMenu(ItemInterface $item);
}