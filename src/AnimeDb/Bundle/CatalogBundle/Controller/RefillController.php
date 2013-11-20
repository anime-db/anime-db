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
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\Refiller;
use AnimeDb\Bundle\CatalogBundle\Form\Plugin\Refiller\Episodes as EpisodesForm;
use AnimeDb\Bundle\CatalogBundle\Form\Plugin\Refiller\Gengres as GengresForm;
use AnimeDb\Bundle\CatalogBundle\Form\Plugin\Refiller\Names as NamesForm;
use AnimeDb\Bundle\CatalogBundle\Form\Plugin\Refiller\Summary as SummaryForm;
use Symfony\Component\HttpFoundation\Request;

/**
 * Refill
 *
 * @package AnimeDb\Bundle\CatalogBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class RefillController extends Controller
{
    /**
     * Refill item
     *
     * @param string $plugin
     * @param string $field
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function refillAction($plugin, $field, Request $request)
    {
        /* @var $refiller \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\Refiller */
        if (!($refiller = $this->get('anime_db.plugin.refiller')->getPlugin($plugin))) {
            throw $this->createNotFoundException('Plugin \''.$plugin.'\' is not found');
        }
        $item = $this->createForm('anime_db_catalog_entity_item', new Item())
            ->handleRequest($request)
            ->getData();

        $form = $this->getForm($field, clone $item, $refiller->refill($item, $field));

        return $this->render('AnimeDbCatalogBundle:Refill:refill.html.twig', [
            'field' => $field,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Search for refill
     *
     * @param string $plugin
     * @param string $field
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchAction($plugin, $field, Request $request)
    {
        /* @var $refiller \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\Refiller */
        if (!($refiller = $this->get('anime_db.plugin.refiller')->getPlugin($plugin))) {
            throw $this->createNotFoundException('Plugin \''.$plugin.'\' is not found');
        }
        $item = $this->createForm('anime_db_catalog_entity_item', new Item())
            ->handleRequest($request)
            ->getData();

        $result = array();
        if ($refiller->isCanSearch($item, $field)) {
            $result = $refiller->search($item, $field);
            /* @var $search_item \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\Item */
            foreach ($result as $key => $search_item) {
                $result[$key] = [
                    'name' => $search_item->getName(),
                    'link' => $this->generateUrl('refiller_search_fill', [
                        'plugin' => $plugin,
                        'field' => $field,
                        'id' => $item->getId(),
                        'data' => $search_item->getData(),
                        'source' => $search_item->getSource(),
                    ])
                ];
            }
        }

        return $this->render('AnimeDbCatalogBundle:Refill:search.html.twig', [
            'result' => $result
        ]);
    }

    /**
     * Refill item from search result
     *
     * @param string $plugin
     * @param string $field
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function fillFromSearchAction($plugin, $field, Request $request)
    {
        /* @var $refiller \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\Refiller */
        if (!($refiller = $this->get('anime_db.plugin.refiller')->getPlugin($plugin))) {
            throw $this->createNotFoundException('Plugin \''.$plugin.'\' is not found');
        }
        $item = $this->createForm('anime_db_catalog_entity_item', new Item())
            ->handleRequest($request)
            ->getData();

        $form = $this->getForm($field, clone $item, $refiller->refillFromSearchResult($item, $field, $request->get('data')));

        return $this->render('AnimeDbCatalogBundle:Refill:refill.html.twig', [
            'field' => $field,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Get form for field
     *
     * @param string $field
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item_origin
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item_fill
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function getForm($field, Item $item_origin, Item $item_fill)
    {
        switch ($field) {
            case Refiller::FIELD_EPISODES:
                $form = new EpisodesForm();
                $data = ['episodes' => $item_fill->getEpisodes()];
                break;
            case Refiller::FIELD_GENRES:
                $form = new GengresForm();
                $data = ['genres' => $item_fill->getGenres()];
                break;
            case Refiller::FIELD_NAMES:
                $form = new NamesForm();
                $data = ['names' => $item_fill->getNames()];
                break;
            case Refiller::FIELD_SUMMARY:
                $form = new SummaryForm();
                $data = ['summary' => $item_fill->getSummary()];
                break;
            default:
                throw $this->createNotFoundException('Field \''.$field.'\' is not supported');
        }
        // search new source link
        $sources_origin = array_reverse($item_origin->getSources()->toArray());
        $sources_fill = array_reverse($item_fill->getSources()->toArray());
        foreach ($sources_fill as $source_fill) {
            // sources is already added
            foreach ($sources_origin as $source_origin) {
                if ($source_fill->getUrl() == $source_origin->getUrl()) {
                    continue 2;
                }
            }
            $data['source'] = $source->getUrl();
            break;
        }

        return $this->createForm($form, $data);
    }
}