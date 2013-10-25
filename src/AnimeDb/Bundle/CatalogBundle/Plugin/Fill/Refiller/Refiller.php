<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller;

use AnimeDb\Bundle\CatalogBundle\Plugin\Plugin;
use AnimeDb\Bundle\CatalogBundle\Entity\Item;

/**
 * Plugin refiller
 *
 * @package AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class Refiller extends Plugin
{
    /**
     * Item names for refill
     *
     * @var string
     */
    const FIELD_NAMES = 'names';

    /**
     * Item genres for refill
     *
     * @var string
     */
    const FIELD_GENRES = 'genres';

    /**
     * Item list of episodes for refill
     *
     * @var string
     */
    const FIELD_EPISODES = 'episodes';

    /**
     * Item description for refill
     *
     * @var string
     */
    const FIELD_DESCRIPTION = 'description';

    /**
     * Is can refill item from source
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     * @param string $field
     *
     * @return boolean
     */
    abstract public function isCanRefillFromSource(Item $item, $field);

    /**
     * Refill item field from source
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     * @param string $field
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    abstract public function refillFromSource(Item $item, $field);

    /**
     * Is can search
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     * @param string $field
     *
     * @return boolean
     */
    abstract public function isCanSearch(Item $item, $field);

    /**
     * Search items for refill
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     * @param string $field
     *
     * @return array [\AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\Item]
     */
    abstract public function search(Item $item, $field);

    /**
     * Refill item field from search result
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     * @param string $field
     * @param array $data
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    abstract public function refillFromSearchResult(Item $item, $field, array $data);

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
            'route' => 'item_refiller',
            'routeParameters' => ['plugin' => $this->getName()]
        ]);
    }
}