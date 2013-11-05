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
use AnimeDb\Bundle\CatalogBundle\Form\Entity\Item as ItemForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Fill
 *
 * @package AnimeDb\Bundle\CatalogBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class FillController extends Controller
{
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
        /* @var $chain \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Chain */
        $chain = $this->get('anime_db.plugin.filler');
        if (!($filler = $chain->getPlugin($plugin))) {
            throw $this->createNotFoundException('Plugin \''.$plugin.'\' is not found');
        }

        /* @var $form \Symfony\Component\Form\Form */
        $form = $this->createForm($filler->getForm());

        $fill_form = null;
        $form->handleRequest($request);
        if ($form->isValid()) {
            $item = $filler->fill($form->getData());
            if (!($item instanceof Item)) {
                throw new \Exception('Can`t get content from the specified source');
            }
            $fill_form = $this->createForm(new ItemForm(), $item)->createView();
        }

        return $this->render('AnimeDbCatalogBundle:Fill:filler.html.twig', [
            'plugin' => $plugin,
            'form' => $form->createView(),
            'fill_form' => $fill_form,
        ]);
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
        /* @var $search \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Search\Search */
        if (!($search = $this->get('anime_db.plugin.search_fill')->getPlugin($plugin))) {
            throw $this->createNotFoundException('Plugin \''.$plugin.'\' is not found');
        }

        /* @var $form \Symfony\Component\Form\Form */
        $form = $this->createForm($search->getForm());

        $list = [];
        $form->handleRequest($request);
        if ($form->isValid()) {
            $list = $search->search($form->getData());
        }

        return $this->render('AnimeDbCatalogBundle:Fill:search.html.twig', [
            'plugin' => $plugin,
            'list'   => $list,
            'form'   => $form->createView()
        ]);
    }

    /**
     * Refill item
     *
     * @param string $plugin
     * @param string $field
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function refillAction($plugin, $field, Item $item, Request $request)
    {
        /* @var $refiller \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\Refiller */
        if (!($refiller = $this->get('anime_db.plugin.refiller')->getPlugin($plugin))) {
            throw $this->createNotFoundException('Plugin \''.$plugin.'\' is not found');
        }
        $search = $this->generateUrl(
            'fill_refiller_search',
            ['plugin' => $plugin, 'field' => $field, 'id' => $item->getId()]
        );

        if ($refiller->isCanRefillFromSource($item, $field)) {
            return new JsonResponse(['item' => $refiller->refillFromSource($item, $field), 'search' => $search]);
        }
        return new JsonResponse(['item' => null, 'search' => $search]);
    }

    /**
     * Refill search item
     *
     * @param string $plugin
     * @param string $field
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function refillSearchAction($plugin, $field, Item $item, Request $request)
    {
        /* @var $refiller \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\Refiller */
        if (!($refiller = $this->get('anime_db.plugin.refiller')->getPlugin($plugin))) {
            throw $this->createNotFoundException('Plugin \''.$plugin.'\' is not found');
        }
        if ($refiller->isCanSearch($item, $field)) {
            return new JsonResponse($refiller->search($item, $field));
        }
        return new JsonResponse([]);
    }
}