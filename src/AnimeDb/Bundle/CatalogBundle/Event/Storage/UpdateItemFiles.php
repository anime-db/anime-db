<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Event\Storage;

use Symfony\Component\EventDispatcher\Event;
use AnimeDb\Bundle\CatalogBundle\Entity\Item;

/**
 * Event thrown when a change is detected item
 *
 * @package AnimeDb\Bundle\CatalogBundle\Event\Storage
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class UpdateItemFiles extends Event
{
    /**
     * Item
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    protected $item;

    /**
     * Construct
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     */
    public function __construct(Item $item)
    {
        $this->item = $item;
    }

    /**
     * Get item
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    public function getItem()
    {
        return $this->item;
    }
}