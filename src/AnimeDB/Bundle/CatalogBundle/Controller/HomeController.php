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
use Symfony\Component\HttpFoundation\JsonResponse;
use AnimeDB\Bundle\CatalogBundle\Form\SearchSimple;
use AnimeDB\Bundle\CatalogBundle\Form\Search;
use Symfony\Component\HttpFoundation\Request;
use AnimeDB\Bundle\CatalogBundle\Entity\Type as TypeEntity;
use AnimeDB\Bundle\CatalogBundle\Entity\Country as CountryEntity;
use AnimeDB\Bundle\CatalogBundle\Entity\Genre as GenreEntity;
use AnimeDB\Bundle\CatalogBundle\Entity\Storage as StorageEntity;
use Doctrine\ORM\Query\Expr;
use AnimeDB\Bundle\CatalogBundle\Service\Pagination;

/**
 * Main page of the catalog
 *
 * @package AnimeDB\Bundle\CatalogBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class HomeController extends Controller
{
    /**
     * Items per page on home page
     *
     * @var integer
     */
    const HOME_ITEMS_PER_PAGE = 8;

    /**
     * Items per page on search page
     *
     * @var integer
     */
    const SEARCH_ITEMS_PER_PAGE = 6;

    /**
     * Home
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        // current page for paging
        $page = $request->get('page', 1);
        $current_page = $page > 1 ? $page : 1;

        // get items
        $repository = $this->getDoctrine()->getRepository('AnimeDBCatalogBundle:Item');
        $items = $repository->createQueryBuilder('i')
            ->orderBy('i.id', 'DESC')
            ->setFirstResult(($current_page - 1) * self::HOME_ITEMS_PER_PAGE)
            ->setMaxResults(self::HOME_ITEMS_PER_PAGE)
            ->getQuery()
            ->getResult();

        // get count all items
        $count = $repository->createQueryBuilder('i')
            ->select('count(i.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $that = $this;
        $pagination = $this->get('anime_db.pagination')->createNavigation(
            ceil($count/self::HOME_ITEMS_PER_PAGE),
            $current_page,
            Pagination::DEFAULT_LIST_LENGTH,
            function ($page) use ($that) {
                return $that->generateUrl('home', ['page' => $page]);
            },
            $this->generateUrl('home')
        );

        return $this->render('AnimeDBCatalogBundle:Home:index.html.twig',
            ['items' => $items, 'pagination' => $pagination]
        );
    }

    /**
     * Search simple form
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchSimpleFormAction()
    {
        return $this->render('AnimeDBCatalogBundle:Home:searchSimpleForm.html.twig', [
            'form' => $this->createForm(new SearchSimple())->createView(),
        ]);
    }

    /**
     * Autocomplete name
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function autocompleteNameAction(Request $request)
    {
        $term = $request->get('term');

        // TODO do search
        $value = ['Foo', 'Bar'];

        return new JsonResponse($value);
    }

    /**
     * Search item
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchAction(Request $request)
    {
        /* @var $form \Symfony\Component\Form\Form */
        $form = $this->createForm(new Search());
        $items = [];
        $pagination = [];

        if ($request->query->count()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();

                // build query
                /* @var $selector \Doctrine\ORM\QueryBuilder */
                $selector = $this->getDoctrine()
                    ->getRepository('AnimeDBCatalogBundle:Item')
                    ->createQueryBuilder('i');

                // main name
                if ($data['name']) {
                    $selector->andWhere('i.name = :name')
                        ->setParameter('name', $data['name']);
                }
                // date start
                if ($data['date_start'] instanceof \DateTime) {
                    $selector->andWhere('i.date_start >= :date_start')
                        ->setParameter('date_start', $data['date_start']->format('Y-m-d'));
                }
                // date end
                if ($data['date_end'] instanceof \DateTime) {
                    $selector->andWhere('i.date_end <= :date_end')
                        ->setParameter('date_end', $data['date_end']->format('Y-m-d'));
                }
                // manufacturer
                if ($data['manufacturer'] instanceof CountryEntity) {
                    $selector->andWhere('i.manufacturer = :manufacturer')
                        ->setParameter('manufacturer', $data['manufacturer']->getId());
                }
                // storage
                if ($data['storage'] instanceof StorageEntity) {
                    $selector->andWhere('i.storage = :storage')
                        ->setParameter('storage', $data['storage']->getId());
                }
                // type
                if ($data['type'] instanceof TypeEntity) {
                    $selector->andWhere('i.type = :type')
                        ->setParameter('type', $data['type']->getId());
                }
                // genres
                if ($data['genres']->count()) {
                    $keys = [];
                    foreach ($data['genres'] as $key => $genre) {
                        $keys[] = ':genre'.$key;
                        $selector->setParameter('genre'.$key, $genre->getId());
                    }
                    $selector->innerJoin('i.genres', 'g')
                        ->andWhere('g.id IN ('.implode(',', $keys).')');
                }

                // get count all items
                $count = clone $selector;
                $count = $count
                    ->select('COUNT(DISTINCT i)')
                    ->getQuery()
                    ->getSingleScalarResult();

                // current page for paging
                $current_page = $request->get('page', 1);
                $current_page = $current_page > 1 ? $current_page : 1;

                // get items
                $items = $selector
                    ->setFirstResult(($current_page - 1) * self::SEARCH_ITEMS_PER_PAGE)
                    ->setMaxResults(self::SEARCH_ITEMS_PER_PAGE)
                    ->orderBy('i.'.($data['sort_field'] ?: 'id'), ($data['sort_direction'] ?: 'DESC'))
                    ->groupBy('i')
                    ->getQuery()
                    ->getResult();

                // build pagination
                $that = $this;
                $pagination = $this->get('anime_db.pagination')->createNavigation(
                    ceil($count/self::HOME_ITEMS_PER_PAGE),
                    $current_page,
                    Pagination::DEFAULT_LIST_LENGTH,
                    function ($page) use ($that, $request) {
                        return $that->generateUrl(
                            'home_search',
                            ['search_items' => $request->query->get('search_items'), 'page' => $page]
                        );
                    },
                    $this->generateUrl('home_search', ['search_items' => $request->query->get('search_items')])
                );
            }
        }

        return $this->render('AnimeDBCatalogBundle:Home:search.html.twig', [
            'form'  => $form->createView(),
            'items' => $items,
            'pagination' => $pagination
        ]);
    }
}