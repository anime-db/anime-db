<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\Bundle\CatalogBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Item repository
 *
 * @package AnimeDB\Bundle\CatalogBundle\Repository
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Item extends EntityRepository
{
    /**
     * Get count items
     *
     * @return integer
     */
    public function getCount()
    {
        return $this->getEntityManager()->createQuery('
            SELECT
                COUNT(i)
            FROM
                AnimeDBCatalogBundle:Item i
        ')->getSingleScalarResult();
    }

    /**
     * Get items list
     *
     * @param integer|null $limit
     * @param integer|null $offset
     *
     * @return array [\AnimeDB\Bundle\CatalogBundle\Entity\Item]
     */
    public function getList($limit = 0, $offset = 0)
    {
        $query = $this->getEntityManager()
            ->createQueryBuilder()
            ->from('AnimeDBCatalogBundle:Item', 'i')
            ->select('i')
            ->orderBy('i.id', 'DESC');

        if ($limit) {
            $query
                ->setFirstResult($offset)
                ->setMaxResults($limit);
        }
        return $query->getQuery()->getResult();
    }
}