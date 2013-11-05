<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AnimeDb\Bundle\CatalogBundle\Entity\Item;
use AnimeDb\Bundle\CatalogBundle\Entity\Name;
use AnimeDb\Bundle\CatalogBundle\Entity\Image;
use AnimeDb\Bundle\CatalogBundle\Entity\Source;
use AnimeDb\Bundle\CatalogBundle\Form\Entity\Item as ItemForm;
use Symfony\Component\HttpFoundation\Request;

/**
 * Item
 *
 * @package AnimeDb\Bundle\CatalogBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ItemController extends Controller
{
    /**
     * Name of session to store item to be added
     *
     * @var string
     */
    const NAME_ITEM_ADDED = '_item_added';

    /**
     * Show item
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Item $item)
    {
        return $this->render('AnimeDbCatalogBundle:Item:show.html.twig', ['item' => $item]);
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

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                /* @var $repository \AnimeDb\Bundle\CatalogBundle\Repository\Item */
                $repository = $this->getDoctrine()->getRepository('AnimeDbCatalogBundle:Item');

                // Add a new entry only if no duplicates
                $duplicate = $repository->findDuplicate($item);
                if ($duplicate) {
                    $request->getSession()->set(self::NAME_ITEM_ADDED, $item);
                    return $this->redirect($this->generateUrl('item_duplicate'));
                } else {
                    return $this->addItem($item);
                }
            }
        }

        return $this->render('AnimeDbCatalogBundle:Item:add-manually.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Change item
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function changeAction(Item $item, Request $request)
    {
        /* @var $form \Symfony\Component\Form\Form */
        $form = $this->createForm(new ItemForm($this->get('anime_db.plugin.refiller')), $item);

        if ($request->isMethod('POST')) {
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

        return $this->render('AnimeDbCatalogBundle:Item:change.html.twig', [
            'item' => $item,
            'form' => $form->createView()
        ]);
    }

    /**
     * Delete item
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Item $item)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($item);
        $em->flush();
        return $this->redirect($this->generateUrl('home'));
    }

    /**
     * Import items
     *
     * @param string $plugin
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function importAction($plugin, Request $request)
    {
        /* @var $chain \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Chain */
        $chain = $this->get('anime_db.plugin.import');
        if (!($import = $chain->getPlugin($plugin))) {
            throw $this->createNotFoundException('Plugin \''.$plugin.'\' is not found');
        }

        $form = $this->createForm($import->getForm());

        $list = [];
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                // import items
                $list = (array)$import->import($form->getData());

                // persist entity
                $em = $this->getDoctrine()->getManager();
                foreach ($list as $key => $item) {
                    if ($item instanceof Item) {
                        $em->persist($item);
                    } else {
                        unset($list[$key]);
                    }
                }
            }
        }

        return $this->render('AnimeDbCatalogBundle:Item:import.html.twig', [
            'plugin' => $plugin,
            'items'  => $list,
            'form'   => $form->createView()
        ]);
    }

    /**
     * Confirm duplicate item
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function duplicateAction(Request $request) {
        /* @var $repository \AnimeDb\Bundle\CatalogBundle\Repository\Item */
        $repository = $this->getDoctrine()->getRepository('AnimeDbCatalogBundle:Item');

        // get store item
        $item = $request->getSession()->get(self::NAME_ITEM_ADDED);
        if (!($item instanceof Item)) {
            throw $this->createNotFoundException('Not found item for confirm duplicate');
        }

        // confirm duplicate
        if ($request->isMethod('POST')) {
            $request->getSession()->remove(self::NAME_ITEM_ADDED);
            switch ($request->request->get('do')) {
                case 'add':
                    $item->freez($this->getDoctrine()->getManager());
                    return $this->addItem($item);
                    break;
                case 'cancel':
                default:
                    return $this->redirect($this->generateUrl('home'));
            }
        }

        // re searching for duplicates
        $duplicate = $repository->findDuplicate($item);
        // now there is no duplication
        if (!$duplicate) {
            $item->freez($this->getDoctrine()->getManager());
            return $this->addItem($item);
        }

        return $this->render('AnimeDbCatalogBundle:Item:duplicate.html.twig', [
            'items' => $duplicate
        ]);
    }

    /**
     * Add item
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function addItem(Item $item)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($item);
        $em->flush();
        return $this->redirect($this->generateUrl(
            'item_show',
            ['id' => $item->getId(), 'name' => $item->getName()]
        ));
    }
}