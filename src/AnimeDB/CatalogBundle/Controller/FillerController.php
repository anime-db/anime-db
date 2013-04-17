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
use AnimeDB\CatalogBundle\Form\Filler\Search;

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
        /* @var $chain \AnimeDB\CatalogBundle\Service\Autofill\Chain */
        $chain = $this->get('anime_db_catalog.autofill.chain');

        /* @var $form \Symfony\Component\Form\Form */
        $form = $this->createForm(new Search($chain->getFillerTitles()));

        /* @var $request \Symfony\Component\HttpFoundation\Request */
        $request = $this->getRequest();
        $list = array();

        if ($request->isMethod('POST')) {
            $form->bindRequest($request);
            if ($form->isValid()) {

                $data = $form->getData();
                /* @var $filler \AnimeDB\CatalogBundle\Service\Autofill\Filler\Filler */
                $filler = $chain->getFiller($data['filler']);
                $list = $filler->search($data['name']);
            }
        }
        return $this->render('AnimeDBCatalogBundle:Filler:search.html.twig', array(
            'list' => $list
        ));
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