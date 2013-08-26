<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\Bundle\CatalogBundle\Service\Plugin;

use Knp\Menu\ItemInterface;

/**
 * Custom menu interface
 * 
 * @package AnimeDB\Bundle\CatalogBundle\Service\Plugin
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
interface CustomMenu
{

    /**
     * Строит меню для плагина
     *
     * @param \Knp\Menu\ItemInterface $item
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function buildMenu(ItemInterface $item);
}