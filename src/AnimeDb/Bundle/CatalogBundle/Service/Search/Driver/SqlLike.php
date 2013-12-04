<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Service\Search\Driver;

use AnimeDb\Bundle\CatalogBundle\Entity\Search;
use AnimeDb\Bundle\CatalogBundle\Service\Search\Driver as DriverSearch;
use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * Search driver use a SQL LIKE for select name
 *
 * @package AnimeDb\Bundle\CatalogBundle\Service\Search\Driver
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class SqlLike implements DriverSearch
{
    /**
     * Item repository
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Repository\Item
     */
    protected $repository;

    /**
     * Construct
     *
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     */
    public function __construct(Registry $doctrine)
    {
        $this->repository = $doctrine->getRepository('AnimeDbCatalogBundle:Item');
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
    public function search(Search $data, $limit = 0, $offset = 0) {
        return [
            'list'  => [],
            'total' => []
        ];
    }

    /**
     * Search items by name
     * 
     * @param string $name
     * @param integer $limit
     */
    public function searchByName($name, $limit = 0) {
        return [];
    }
}