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

        $value = null;
        if ($refiller->isCanRefill($item, $field)) {
            $value = $this->getFieldValue($refiller, $item, $field);
        }

        return new JsonResponse([
            'value' => $value,
            'search' => $this->generateUrl(
                'refiller_search',
                ['plugin' => $plugin, 'field' => $field, 'id' => $item->getId()]
            )
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
        $new_item = $plugin->refill($item, $field);
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