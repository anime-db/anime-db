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
use Knp\Menu\ItemInterface;
use AnimeDb\Bundle\CatalogBundle\Entity\Item;

/**
 * Plugin item interface
 * 
 * @package AnimeDb\Bundle\CatalogBundle\Plugin\Item
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class Item extends Plugin
{
    /**
     * Build menu for plugin
     *
     * @param \Knp\Menu\ItemInterface $node
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     *
     * @return \Knp\Menu\ItemInterface
     */
    abstract public function buildMenu(ItemInterface $node, Item $item);
}