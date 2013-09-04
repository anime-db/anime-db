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
 * Storage repository
 *
 * @package AnimeDB\Bundle\CatalogBundle\Repository
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Storage extends EntityRepository
{
    /**
     * Get storage list
     *
     * @return array [\AnimeDB\Bundle\CatalogBundle\Entity\Storage]
     */
    public function getList()
    {
        return $this->getEntityManager()->createQuery('
            SELECT
                s
            FROM
                AnimeDBCatalogBundle:Storage s
            ORDER BY
                s.id DESC
        ')->getResult();
    }
}