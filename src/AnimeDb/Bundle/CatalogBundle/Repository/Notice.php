<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AnimeDb\Bundle\CatalogBundle\Entity\Notice as NoticeEntity;

/**
 * Notice repository
 *
 * @package AnimeDb\Bundle\CatalogBundle\Repository
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Notice extends EntityRepository
{
    /**
     * Get first show notice
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Notice|null
     */
    public function getFirstShow()
    {
        return $this->getEntityManager()->createQuery('
            SELECT
                n
            FROM
                AnimeDbCatalogBundle:Notice n
            WHERE
                n.status != :closed AND
                (n.date_closed IS NULL OR n.date_closed >= :time)
            ORDER BY
                n.date_created, n.id ASC
        ')
            ->setMaxResults(1)
            ->setParameter('closed', NoticeEntity::STATUS_CLOSED)
            ->setParameter('time', date('Y-m-d H:i:s'))
            ->getOneOrNullResult();
    }

    /**
     * Get notice list
     *
     * @param integer $limit
     * @param integer|null $offset
     *
     * @return array [\AnimeDb\Bundle\CatalogBundle\Entity\Notice]
     */
    public function getList($limit, $offset = 0)
    {
        return $this->getEntityManager()->createQuery('
            SELECT
                n
            FROM
                AnimeDbCatalogBundle:Notice n
            ORDER BY
                n.date_created DESC
        ')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getResult();
    }

    /**
     * Get count notices
     *
     * @return integer
     */
    public function count()
    {
        return $this->getEntityManager()->createQuery('
            SELECT
                COUNT(n)
            FROM
                AnimeDbCatalogBundle:Notice n
        ')->getSingleScalarResult();
    }
}