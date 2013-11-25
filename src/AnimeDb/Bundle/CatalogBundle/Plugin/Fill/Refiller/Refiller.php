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
     * Item summary for refill
     *
     * @var string
     */
    const FIELD_SUMMARY = 'summary';

    /**
     * Item date start for refill
     *
     * @var string
     */
    const FIELD_DATE_START = 'date_start';

    /**
     * Item date end for refill
     *
     * @var string
     */
    const FIELD_DATE_END = 'date_end';

    /**
     * Item manufacturer for refill
     *
     * @var string
     */
    const FIELD_MANUFACTURER = 'manufacturer';

    /**
     * Item duration for refill
     *
     * @var string
     */
    const FIELD_DURATION = 'duration';

    /**
     * Item file info for refill
     *
     * @var string
     */
    const FIELD_FILE_INFO = 'file_info';

    /**
     * Item sources for refill
     *
     * @var string
     */
    const FIELD_SOURCES = 'sources';

    /**
     * Item episodes number for refill
     *
     * @var string
     */
    const FIELD_EPISODES_NUMBER = 'episodes_number';

    /**
     * Item images for refill
     *
     * @var string
     */
    const FIELD_IMAGES = 'images';

    /**
     * Item translate for refill
     *
     * @var string
     */
    const FIELD_TRANSLATE = 'translate';

    /**
     * Is can refill item
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     * @param string $field
     *
     * @return boolean
     */
    abstract public function isCanRefill(Item $item, $field);

    /**
     * Refill item field
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     * @param string $field
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    abstract public function refill(Item $item, $field);

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
}