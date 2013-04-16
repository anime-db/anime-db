<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\CatalogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Filler item
 *
 * @package AnimeDB\CatalogBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class FillerController extends Controller
{
    /**
     * Search item
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchAction() {
        // TODO требуется реализация
        return $this->render('AnimeDBCatalogBundle:Filler:search.html.twig');
    }

    /**
     * Get item
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAction() {
        // TODO требуется реализация
        return $this->render('AnimeDBCatalogBundle:Filler:get.html.twig');
    }
}