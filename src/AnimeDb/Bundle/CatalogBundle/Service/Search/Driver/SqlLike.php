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
use AnimeDb\Bundle\CatalogBundle\Service\Search\Manager;

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
     * @param string $sort_column
     * @param string $sort_direction
     *
     * @return array {list:[],total:0}
     */
    public function search(Search $data, $limit, $offset, $sort_column, $sort_direction)
    {
        /* @var $selector \Doctrine\ORM\QueryBuilder */
        $selector = $this->repository->createQueryBuilder('i');

        // main name
        if ($data->getName()) {
            // TODO create index name for rapid and accurate search
            $selector
                ->innerJoin('i.names', 'n')
                ->andWhere('i.name LIKE :name OR n.name LIKE :name')
                ->setParameter('name', str_replace('%', '%%', $data->getName()).'%');
        }
        // date start
        if ($data->getDateStart() instanceof \DateTime) {
            $selector->andWhere('i.date_start >= :date_start')
                ->setParameter('date_start', $data->getDateStart()->format('Y-m-d'));
        }
        // date end
        if ($data->getDateEnd() instanceof \DateTime) {
            $selector->andWhere('i.date_end <= :date_end')
                ->setParameter('date_end', $data->getDateEnd()->format('Y-m-d'));
        }
        // manufacturer
        if ($data->getManufacturer() instanceof CountryEntity) {
            $selector->andWhere('i.manufacturer = :manufacturer')
                ->setParameter('manufacturer', $data->getManufacturer()->getId());
        }
        // storage
        if ($data->getStorage() instanceof StorageEntity) {
            $selector->andWhere('i.storage = :storage')
                ->setParameter('storage', $data->getStorage()->getId());
        }
        // type
        if ($data->getType() instanceof TypeEntity) {
            $selector->andWhere('i.type = :type')
                ->setParameter('type', $data->getType()->getId());
        }
        // genres
        if ($data->getGenres()->count()) {
            $keys = [];
            foreach ($data->getGenres() as $key => $genre) {
                $keys[] = ':genre'.$key;
                $selector->setParameter('genre'.$key, $genre->getId());
            }
            $selector->innerJoin('i.genres', 'g')
                ->andWhere('g.id IN ('.implode(',', $keys).')');
        }

        // get count all items
        $total = clone $selector;
        $total = $total
            ->select('COUNT(DISTINCT i)')
            ->getQuery()
            ->getSingleScalarResult();

        // apply order
        $selector
            ->orderBy('i.'.$sort_column, $sort_direction)
            ->addOrderBy('i.id', $sort_direction);

        if ($offset) {
            $selector->setFirstResult($offset);
        }
        if ($limit) {
            $selector->setMaxResults($limit);
        }

        // get items
        $list = $selector
            ->groupBy('i')
            ->getQuery()
            ->getResult();

        return [
            'list' => $list,
            'total' => $total
        ];
    }

    /**
     * Search items by name
     * 
     * @param string $name
     * @param integer $limit
     */
    public function searchByName($name, $limit = 0)
    {
        if (!$name) {
            return [];
        }

        /* @var $selector \Doctrine\ORM\QueryBuilder */
        $selector = $this->repository->createQueryBuilder('i');
        $selector
            ->innerJoin('i.names', 'n')
            ->andWhere('i.name LIKE :name OR n.name LIKE :name')
            ->setParameter('name', str_replace('%', '%%', $name).'%');

        if ($limit > 0) {
            $selector->setMaxResults($limit);
        }

        // get items
        return $selector
            ->groupBy('i')
            ->getQuery()
            ->getResult();
    }
}