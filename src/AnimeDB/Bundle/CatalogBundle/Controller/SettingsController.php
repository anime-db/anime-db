<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\Bundle\CatalogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Application settings
 *
 * @package AnimeDB\Bundle\CatalogBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class SettingsController extends Controller
{
    /**
     * Storages list
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function storagesAction() {
        $repository = $this->getDoctrine()->getRepository('AnimeDBCatalogBundle:Storage');
        $storages = $repository->createQueryBuilder('s')
            ->orderBy('s.id', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('AnimeDBCatalogBundle:Settings:storages.html.twig',
            ['storages' => $storages]
        );
    }
}