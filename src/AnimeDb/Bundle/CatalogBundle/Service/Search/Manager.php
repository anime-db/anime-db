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
     * Deafult sort column
     *
     * @var string
     */
    const DEFAULT_SORT_COLUMN = 'date_update';

    /**
     * Deafult sort direction
     *
     * @var string
     */
    const DEFAULT_SORT_DIRECTION = 'DESC';

    /**
     * Search driver
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Service\Search\Driver
     */
    protected $driver;

    /**
     * Sort columns
     *
     * @var array
     */
    public static $sort_columns = [
        'name',
        'date_update',
        'date_start',
        'date_end'
    ];

    /**
     * Sort direction
     *
     * @var array
     */
    public static $sort_direction = [
        'DESC',
        'ASC'
    ];

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
     * @param integer|null $limit
     * @param integer|null $offset
     * @param string|null $sort_column
     * @param string|null $sort_direction
     *
     * @return array {list:[],total:0}
     */
    public function search(
        Search $data,
        $limit = 0,
        $offset = 0,
        $sort_column = self::DEFAULT_SORT_COLUMN,
        $sort_direction = self::DEFAULT_SORT_DIRECTION
    ) {
        return $this->driver->search(
            $data,
            ($limit > 0 ? (int)$limit : 0),
            ($offset > 0 ? (int)$offset : 0),
            self::getValidSortColumn($sort_column),
            self::getValidSortDirection($sort_direction)
        );
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

    /**
     * Get valid sort column
     *
     * @param string|null $column
     *
     * @return string
     */
    public static function getValidSortColumn($column = self::DEFAULT_SORT_COLUMN)
    {
        return in_array($column, self::$sort_columns) ? $column : self::DEFAULT_SORT_COLUMN;
    }

    /**
     * Get valid sort direction
     *
     * @param string|null $direction
     *
     * @return string
     */
    public static function getValidSortDirection($direction = self::DEFAULT_SORT_DIRECTION)
    {
        return in_array($direction, self::$sort_direction) ? $direction : self::DEFAULT_SORT_DIRECTION;
    }
}