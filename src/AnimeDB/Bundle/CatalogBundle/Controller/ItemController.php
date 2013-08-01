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
use AnimeDB\Bundle\CatalogBundle\Form\Filler\Search;
use AnimeDB\Bundle\CatalogBundle\Form\Filler\Get;
use AnimeDB\Bundle\CatalogBundle\Entity\Item;
use AnimeDB\Bundle\CatalogBundle\Entity\Name;
use AnimeDB\Bundle\CatalogBundle\Entity\Image;
use AnimeDB\Bundle\CatalogBundle\Entity\Source;
use AnimeDB\Bundle\CatalogBundle\Form\Entity\Item as ItemForm;
use Symfony\Component\HttpFoundation\Request;

/**
 * Item
 *
 * @package AnimeDB\Bundle\CatalogBundle\Controller
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
        /* @var $item \AnimeDB\Bundle\CatalogBundle\Entity\Item */
        $item = $this->getDoctrine()
            ->getRepository('AnimeDBCatalogBundle:Item')
            ->find($id);
        if (!$item) {
            throw $this->createNotFoundException('No found items for id '.$id);
        }
        return $this->render('AnimeDBCatalogBundle:Item:show.html.twig', ['item' => $item]);
    }

    /**
     * Add item
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction()
    {
        /* @var $chain \AnimeDB\Bundle\CatalogBundle\Service\Autofill\Chain */
        $chain = $this->get('anime_db.autofill');

        /* @var $search \Symfony\Component\Form\Form */
        $search = $this->createForm(new Search($chain->getFillerTitles()));

        /* @var $source \Symfony\Component\Form\Form */
        $source = $this->createForm(new Get());

        return $this->render('AnimeDBCatalogBundle:Item:add.html.twig', [
            'source_form' => $source->createView(),
            'search_form' => $search->createView(),
        ]);
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
        $form = $this->createForm(new ItemForm(), $item);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($item);
                $em->flush();
                return $this->redirect($this->generateUrl(
                    'item_show',
                    ['id' => $item->getId(), 'name' => $item->getName()]
                ));
            }
        }

        return $this->render('AnimeDBCatalogBundle:Item:add-manually.html.twig', [
            'form' => $form->createView()
        ]);
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
        /* @var $item \AnimeDB\Bundle\CatalogBundle\Entity\Item */
        $item = $this->getDoctrine()
            ->getRepository('AnimeDBCatalogBundle:Item')
            ->find($id);
        if (!$item) {
            throw $this->createNotFoundException('No found items for id '.$id);
        }

        /* @var $form \Symfony\Component\Form\Form */
        $form = $this->createForm(new ItemForm(), $item);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($item);
                $em->flush();
                return $this->redirect($this->generateUrl(
                    'item_show',
                    ['id' => $item->getId(), 'name' => $item->getName()]
                ));
            }
        }

        return $this->render('AnimeDBCatalogBundle:Item:change.html.twig', [
            'item' => $item,
            'form' => $form->createView()
        ]);
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
        /* @var $item \AnimeDB\Bundle\CatalogBundle\Entity\Item */
        $item = $this->getDoctrine()
            ->getRepository('AnimeDBCatalogBundle:Item')
            ->find($id);
        if (!$item) {
            throw $this->createNotFoundException('No found items for id '.$id);
        }

        // TODO requires the implementation complement directory

        return $this->redirect($this->generateUrl(
            'item_show',
            ['id' => $item->getId(), 'name' => $item->getName()]
        ));
    }
}