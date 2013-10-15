<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Plugin\Import;

use AnimeDb\Bundle\CatalogBundle\Plugin\Plugin;
use Knp\Menu\ItemInterface;

/**
 * Plugin import
 * 
 * @package AnimeDb\Bundle\CatalogBundle\Plugin\Import
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class Import extends Plugin
{
    /**
     * Get form
     *
     * @return \Symfony\Component\Form\AbstractType
     */
    abstract public function getForm();

    /**
     * Import items from source data
     *
     * @param array $data
     *
     * @return array [ \AnimeDb\Bundle\CatalogBundle\Entity\Item ]
     */
    abstract public function import(array $data);

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
            'route' => 'item_import',
            'routeParameters' => ['plugin' => $this->getName()]
        ]);
    }
}