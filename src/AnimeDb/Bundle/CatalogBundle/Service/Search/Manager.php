<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Service\Search;

use AnimeDb\Bundle\CatalogBundle\Service\Search\Driver;
use AnimeDb\Bundle\CatalogBundle\Entity\Search;

/**
 * Item search
 *
 * @package AnimeDb\Bundle\CatalogBundle\Service\Search
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Manager
{
    /**
     * Search driver
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Service\Search\Driver
     */
    protected $driver;

    /**
     * Construct
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Service\Search\Driver $driver
     */
    public function __construct(Driver $driver)
    {
        $this->driver = $driver;
    }

    /**
     * Search items
     * 
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Search $data
     * @param integer $limit
     * @param integer $offset
     *
     * @return array {list:[],total:0}
     */
    public function search(Search $data, $limit = 0, $offset = 0)
    {
        return $this->driver->search($data, $limit, $offset);
    }

    /**
     * Search items by name
     * 
     * @param string $name
     * @param integer $limit
     */
    public function searchByName($name, $limit = 0)
    {
        return $this->driver->searchByName($name, $limit);
    }
}