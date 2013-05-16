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
        $chain = $this->get('anime_db.autofill');

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
    public function fillAction() {
        /* @var $form \Symfony\Component\Form\Form */
        $form = $this->createForm(new Get());

        /* @var $request \Symfony\Component\HttpFoundation\Request */
        $request = $this->getRequest();

        $error = '';
        $fill_form = null;
        $form->bindRequest($request);
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
                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($item);
                    $fill_form = $this->createForm(new ItemType(), $item)->createView();
                }
            }
        }

        return $this->render('AnimeDBCatalogBundle:Filler:fill.html.twig', array(
            'error' => $error,
            'form' => $fill_form,
        ));
    }
}