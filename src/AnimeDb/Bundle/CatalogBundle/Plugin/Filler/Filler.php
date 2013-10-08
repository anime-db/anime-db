<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Plugin\Filler;

use AnimeDb\Bundle\CatalogBundle\Plugin\Plugin;
use Knp\Menu\ItemInterface;

/**
 * Plugin filler
 * 
 * @package AnimeDb\Bundle\CatalogBundle\Plugin\Filler
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class Filler extends Plugin
{
    /**
     * Fill item from source
     *
     * @param string $source
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item|null
     */
    abstract public function fill($source);

    /**
     * Filler is support this source
     *
     * @param string $source
     *
     * @return boolean
     */
    abstract public function isSupportSource($source);

    /**
     * Build menu for plugin
     *
     * @param \Knp\Menu\ItemInterface $item
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function buildMenu(ItemInterface $item)
    {
        $item->addChild($this->getTitle(), [
            'route' => 'item_filler',
            'routeParameters' => ['plugin' => $this->getName()]
        ]);
    }
}