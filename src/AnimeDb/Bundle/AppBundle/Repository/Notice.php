<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AnimeDb\Bundle\AppBundle\Entity\Notice as NoticeEntity;

/**
 * Notice repository
 *
 * @package AnimeDb\Bundle\AppBundle\Repository
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Notice extends EntityRepository
{
    /**
     * Get first show notice
     *
     * @return \AnimeDb\Bundle\AppBundle\Entity\Notice|null
     */
    public function getFirstShow()
    {
        return $this->getEntityManager()->createQuery('
            SELECT
                n
            FROM
                AnimeDbAppBundle:Notice n
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
     * @return array [\AnimeDb\Bundle\AppBundle\Entity\Notice]
     */
    public function getList($limit, $offset = 0)
    {
        return $this->getEntityManager()->createQuery('
            SELECT
                n
            FROM
                AnimeDbAppBundle:Notice n
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
                AnimeDbAppBundle:Notice n
        ')->getSingleScalarResult();
    }
}