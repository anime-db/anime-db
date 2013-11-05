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
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\Refiller;

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
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function refillerAction($plugin, $field, Item $item)
    {
        /* @var $refiller \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\Refiller */
        if (!($refiller = $this->get('anime_db.plugin.refiller')->getPlugin($plugin))) {
            throw $this->createNotFoundException('Plugin \''.$plugin.'\' is not found');
        }

        $value = null;
        if ($refiller->isCanRefillFromSource($item, $field)) {
            $value = $this->getFieldValue($refiller, $item, $field);
        }

        return new JsonResponse([
            'field' => $field,
            'value' => $value,
            'search' => $this->generateUrl(
                'fill_refiller_search',
                ['plugin' => $plugin, 'field' => $field, 'id' => $item->getId()]
            )
        ]);
    }

    /**
     * Refill search item
     *
     * @param string $plugin
     * @param string $field
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function refillerSearchAction($plugin, $field, Item $item)
    {
        /* @var $refiller \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\Refiller */
        if (!($refiller = $this->get('anime_db.plugin.refiller')->getPlugin($plugin))) {
            throw $this->createNotFoundException('Plugin \''.$plugin.'\' is not found');
        }
        if ($refiller->isCanSearch($item, $field)) {
            $result = $refiller->search($item, $field);
            /* @var $item \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\Item */
            foreach ($result as $key => $search_item) {
                $result[$key] = [
                    'name' => $search_item->getName(),
                    'image' => $search_item->getImage(),
                    'description' => $search_item->getDescription(),
                    'link' => $this->generateUrl('fill_refiller', [
                        'plugin' => $plugin,
                        'field' => $field,
                        'id' => $item->getId(),
                        'data' => $search_item->getData()
                    ])
                ];
            }
            return new JsonResponse($result);
        }
        return new JsonResponse([]);
    }

    /**
     * Get field value
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\Refiller $plugin
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     * @param string $field
     *
     * @return mixed
     */
    protected function getFieldValue(Refiller $plugin, Item $item, $field)
    {
        $new_item = $plugin->refillFromSource($item, $field);
        switch ($field) {
            case Refiller::FIELD_EPISODES:
                return $new_item->getEpisodes();
            case Refiller::FIELD_GENRES:
                $genres = [];
                /* @var $genre \AnimeDb\Bundle\CatalogBundle\Entity\Genre */
                foreach ($new_item->getGenres() as $genre) {
                    $genres[$genre->getId()] = $genre->getName();
                }
                return $genres;
            case Refiller::FIELD_NAMES:
                $names = [];
                /* @var $name \AnimeDb\Bundle\CatalogBundle\Entity\Name */
                foreach ($new_item->getNames() as $name) {
                    $names[$name->getId()] = $name->getName();
                }
                return $names;
            case Refiller::FIELD_SUMMARY:
                return $new_item->getSummary();
            default:
                return null;
        }
    }
}