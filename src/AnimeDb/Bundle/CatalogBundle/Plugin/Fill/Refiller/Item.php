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

/**
 * Element search results
 * 
 * @package AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Item
{
    /**
     * Name
     *
     * @var string
     */
    protected $name = '';

    /**
     * Data for refill item
     *
     * @var array
     */
    protected $data = [];

    /**
     * Source
     *
     * Can set the source to source item to avoid the next search for this item
     *
     * @var string
     */
    protected $source = '';

    /**
     * Construct
     *
     * @param string $name
     * @param array $data
     * @param string $source
     */
    public function __construct($name, array $data, $source)
    {
        $this->name = $name;
        $this->data = $data;
        $this->source = $source;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get data for refill item
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }
}