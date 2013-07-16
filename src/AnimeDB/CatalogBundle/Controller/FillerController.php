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
use AnimeDB\CatalogBundle\Form\ItemType;
use AnimeDB\CatalogBundle\Entity\Item;
use AnimeDB\CatalogBundle\Entity\Name;
use AnimeDB\CatalogBundle\Entity\Image;
use AnimeDB\CatalogBundle\Entity\Source;
use Symfony\Component\HttpFoundation\Request;

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
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchAction(Request $request) {
        /* @var $chain \AnimeDB\CatalogBundle\Service\Autofill\Chain */
        $chain = $this->get('anime_db.autofill');

        /* @var $form \Symfony\Component\Form\Form */
        $form = $this->createForm(new Search($chain->getFillerTitles()));

        $list = array();
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
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
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function fillAction(Request $request) {
        /* @var $form \Symfony\Component\Form\Form */
        $form = $this->createForm(new Get());

        $error = '';
        $fill_form = null;
        $form->handleRequest($request);
        if ($form->isValid()) {
            $source = $form->getData()['url'];
            /* @var $chain \AnimeDB\CatalogBundle\Service\Autofill\Chain */
            $chain = $this->get('anime_db.autofill');
            $filler = $chain->getFillerBySource($source);
            if (!($filler instanceof Filler)) {
                $error = 'Unable to find any filler for the specified source';
            } else {
                /* @var $item \AnimeDB\CatalogBundle\Entity\Item */
                $item = $filler->fill($source);
                if (!$item) {
                    $error = 'Can`t get content from the specified source';
                } else {
                    // persist entity
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($item);
                    $fill_form = $this->createForm(new ItemType(), $item)->createView();
                }
            }
        } else {
            $error = 'Not specified source';
        }

        return $this->render('AnimeDBCatalogBundle:Filler:fill.html.twig', array(
            'error' => $error,
            'form' => $fill_form,
        ));
    }
}