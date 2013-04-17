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
use AnimeDB\CatalogBundle\Form\Filler\Get;
use AnimeDB\CatalogBundle\Service\Autofill\Filler\Filler;

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
        /* @var $form \Symfony\Component\Form\Form */
        $form = $this->createForm(new Get());

        /* @var $request \Symfony\Component\HttpFoundation\Request */
        $request = $this->getRequest();

        $error = '';
        if ($request->isMethod('GET')) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $source = $form->getData()['url'];
                /* @var $chain \AnimeDB\CatalogBundle\Service\Autofill\Chain */
                $chain = $this->get('anime_db_catalog.autofill.chain');
                $filler = $chain->getFillerBySource($source);
                if (!($filler instanceof Filler)) {
                    $error = $this->get('translator')->trans('Unable to find any filler for the specified source');
                } else {
                    $item = $filler->fill($source);
                    // TODO create form from item for chenge data and save
                }
            }
        }

        return $this->render('AnimeDBCatalogBundle:Filler:get.html.twig', array(
            'error' => $error
        ));
    }
}