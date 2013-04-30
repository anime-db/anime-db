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
use AnimeDB\CatalogBundle\Entity\Item;
use AnimeDB\CatalogBundle\Entity\Name;
use AnimeDB\CatalogBundle\Entity\Image;
use AnimeDB\CatalogBundle\Entity\Source;
use AnimeDB\CatalogBundle\Form\ItemType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Item
 *
 * @package AnimeDB\CatalogBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ItemController extends Controller
{
    /**
     * Show item
     *
     * @param integer $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id)
    {
        /* @var $item \AnimeDB\CatalogBundle\Entity\Item */
        $item = $this->getDoctrine()
            ->getRepository('AnimeDBCatalogBundle:Item')
            ->find($id);
        if (!$item) {
            throw $this->createNotFoundException('No found items for id '.$id);
        }
        return $this->render('AnimeDBCatalogBundle:Item:show.html.twig',
            array('item' => $item)
        );
    }

    /**
     * Add item
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction()
    {
        /* @var $chain \AnimeDB\CatalogBundle\Service\Autofill\Chain */
        $chain = $this->get('anime_db_catalog.autofill.chain');

        /* @var $search \Symfony\Component\Form\Form */
        $search = $this->createForm(new Search($chain->getFillerTitles()));

        /* @var $source \Symfony\Component\Form\Form */
        $source = $this->createForm(new Get());

        return $this->render('AnimeDBCatalogBundle:Item:add.html.twig', array(
            'source_form' => $source->createView(),
            'search_form' => $search->createView(),
        ));
    }

    /**
     * Addition form
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addManuallyAction(Request $request)
    {
        $item = new Item();

        /* @var $form \Symfony\Component\Form\Form */
        $form = $this->createForm(new ItemType(), $item);

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($item);
                $em->flush();
                return $this->redirect($this->generateUrl(
                    'item_show',
                    array('id' => $item->getId(), 'name' => $item->getName())
                ));
            }
        }

        return $this->render('AnimeDBCatalogBundle:Item:add-manually.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * Change item
     *
     * @param integer $id
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function changeAction($id, Request $request)
    {
        /* @var $item \AnimeDB\CatalogBundle\Entity\Item */
        $item = $this->getDoctrine()
            ->getRepository('AnimeDBCatalogBundle:Item')
            ->find($id);
        if (!$item) {
            throw $this->createNotFoundException('No found items for id '.$id);
        }

        /* @var $form \Symfony\Component\Form\Form */
        $form = $this->createForm(new ItemType(), $item);

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($item);
                $em->flush();
                return $this->redirect($this->generateUrl(
                    'item_show',
                    array('id' => $item->getId(), 'name' => $item->getName())
                ));
            }
        }

        return $this->render('AnimeDBCatalogBundle:Item:change.html.twig', array(
            'item' => $item,
            'form' => $form->createView()
        ));
    }

    /**
     * Remove item
     *
     * @param integer $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeAction($id)
    {
        // TODO requires the implementation of
        return $this->redirect($this->generateUrl('home'));
    }

    /**
     * Complement item directory
     *
     * @param integer $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function complementAction($id) {
        /* @var $item \AnimeDB\CatalogBundle\Entity\Item */
        $item = $this->getDoctrine()
            ->getRepository('AnimeDBCatalogBundle:Item')
            ->find($id);
        if (!$item) {
            throw $this->createNotFoundException('No found items for id '.$id);
        }

        // TODO requires the implementation complement directory

        return $this->redirect($this->generateUrl(
            'item_show',
            array('id' => $item->getId(), 'name' => $item->getName())
        ));
    }
}