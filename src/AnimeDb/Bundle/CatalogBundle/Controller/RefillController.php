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
use Symfony\Component\HttpFoundation\JsonResponse;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\Refiller;
use AnimeDb\Bundle\CatalogBundle\Form\Plugin\Refiller\Episodes as EpisodesForm;
use AnimeDb\Bundle\CatalogBundle\Form\Plugin\Refiller\Gengres as GengresForm;
use AnimeDb\Bundle\CatalogBundle\Form\Plugin\Refiller\Names as NamesForm;
use AnimeDb\Bundle\CatalogBundle\Form\Plugin\Refiller\Summary as SummaryForm;

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
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function refillAction($plugin, $field, Item $item)
    {
        /* @var $refiller \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\Refiller */
        if (!($refiller = $this->get('anime_db.plugin.refiller')->getPlugin($plugin))) {
            throw $this->createNotFoundException('Plugin \''.$plugin.'\' is not found');
        }

        /* @var $form \Symfony\Component\Form\Form */
        $form = $this->createForm($this->getForm($field), $refiller->refill($item, $field));

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
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchAction($plugin, $field, Item $item)
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
                    'link' => $this->generateUrl('refiller_search_fill', [
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
     * Refill item from search result
     *
     * @param string $plugin
     * @param string $field
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function refillFromSearchAction($plugin, $field, Item $item)
    {
        /* @var $refiller \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller\Refiller */
        if (!($refiller = $this->get('anime_db.plugin.refiller')->getPlugin($plugin))) {
            throw $this->createNotFoundException('Plugin \''.$plugin.'\' is not found');
        }
        return new JsonResponse([]);
    }

    /**
     * Get form for field
     *
     * @param string $field
     *
     * @return \Symfony\Component\Form\AbstractType|null
     */
    protected function getForm($field)
    {
        switch ($field) {
            case Refiller::FIELD_EPISODES:
                return new EpisodesForm();
            case Refiller::FIELD_GENRES:
                return new GengresForm();
            case Refiller::FIELD_NAMES:
                return new NamesForm();
            case Refiller::FIELD_SUMMARY:
                return new SummaryForm();
            default:
                throw $this->createNotFoundException('Field \''.$field.'\' is not supported');
        }
    }
}