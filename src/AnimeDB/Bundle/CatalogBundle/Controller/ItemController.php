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

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AnimeDB\Bundle\CatalogBundle\Form\Filler\Search;
use AnimeDB\Bundle\CatalogBundle\Form\Filler\Get;
use AnimeDB\Bundle\CatalogBundle\Entity\Item;
use AnimeDB\Bundle\CatalogBundle\Entity\Name;
use AnimeDB\Bundle\CatalogBundle\Entity\Image;
use AnimeDB\Bundle\CatalogBundle\Entity\Source;
use AnimeDB\Bundle\CatalogBundle\Form\Entity\Item as ItemForm;
use Symfony\Component\HttpFoundation\Request;
use AnimeDB\Bundle\CatalogBundle\Form\Plugin\Search as SearchPluginForm;
use AnimeDB\Bundle\CatalogBundle\Form\Plugin\Filler as FillerPluginForm;
use AnimeDB\Bundle\CatalogBundle\Service\Plugin\Search\CustomForm as CustomFormSearch;
use AnimeDB\Bundle\CatalogBundle\Service\Plugin\Filler\CustomForm as CustomFormFiller;

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
     * @param \AnimeDB\Bundle\CatalogBundle\Entity\Item $item
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Item $item)
    {
        return $this->render('AnimeDBCatalogBundle:Item:show.html.twig', ['item' => $item]);
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
     * @param \AnimeDB\Bundle\CatalogBundle\Entity\Item $item
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function changeAction(Item $item, Request $request)
    {
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
     * Delete item
     *
     * @param \AnimeDB\Bundle\CatalogBundle\Entity\Item $item
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
     * Create new item from source fill
     *
     * @param string $plugin
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function fillerAction($plugin, Request $request)
    {
        return new Response();
    }

    /**
     * Search source fill for item
     *
     * @param string $plugin
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchAction($plugin, Request $request)
    {
        /* @var $chain \AnimeDB\Bundle\CatalogBundle\Service\Plugin\Search\Chain */
        $chain = $this->get('anime_db.plugin.search');
        if (!($search = $chain->getPlugin($plugin))) {
            throw $this->createNotFoundException('Plugin \''.$plugin.'\' is not found');
        }

        /* @var $form \Symfony\Component\Form\Form */
        if ($search instanceof CustomFormSearch) {
            $form = $this->createForm($search->getForm());
        } else {
            $form = $this->createForm(new SearchPluginForm());
        }

        $list = [];
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                // url bulder for fill items in list
                $that = $this;
                $url_builder = function ($source) use ($that, $plugin) {
                    return $that->generateUrl(
                        'item_filler',
                        [
                            'plugin' => $plugin,
                            (new FillerPluginForm())->getName() => ['url' => $source]
                        ]
                    );
                };

                // search items
                if ($search instanceof CustomFormSearch) {
                    $list = $search->search($form->getData(), $url_builder);
                } else {
                    $list = $search->search($form->getData()['name'], $url_builder);
                }
            }
        }

        return $this->render('AnimeDBCatalogBundle:Item:search.html.twig', [
            'plugin' => $plugin,
            'list'   => $list,
            'form'   => $form->createView()
        ]);
    }

    /**
     * Import items
     *
     * @param string $plugin
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function importAction($plugin)
    {
        return new Response();
    }
}