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
use Symfony\Component\HttpFoundation\JsonResponse;
use AnimeDb\Bundle\CatalogBundle\Form\SearchSimple;
use AnimeDb\Bundle\CatalogBundle\Form\Search as SearchForm;
use Symfony\Component\HttpFoundation\Request;
use AnimeDb\Bundle\CatalogBundle\Entity\Type as TypeEntity;
use AnimeDb\Bundle\CatalogBundle\Entity\Country as CountryEntity;
use AnimeDb\Bundle\CatalogBundle\Entity\Genre as GenreEntity;
use AnimeDb\Bundle\CatalogBundle\Entity\Storage as StorageEntity;
use Doctrine\ORM\Query\Expr;
use AnimeDb\Bundle\AppBundle\Service\Pagination;
use AnimeDb\Bundle\CatalogBundle\Form\Settings\General as GeneralForm;
use AnimeDb\Bundle\CatalogBundle\Entity\Settings\General as GeneralEntity;
use Symfony\Component\Yaml\Yaml;
use AnimeDb\Bundle\CatalogBundle\Service\Listener\Request as RequestListener;
use AnimeDb\Bundle\CatalogBundle\Entity\Search as SearchEntity;
use AnimeDb\Bundle\CatalogBundle\Service\Search\Manager as ManagerSearch;

/**
 * Main page of the catalog
 *
 * @package AnimeDb\Bundle\CatalogBundle\Controller
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
     * Limit for show all items
     *
     * @var integer
     */
    const SHOW_LIMIT_ALL = -1;

    /**
     * Widget place top
     *
     * @var string
     */
    const WIDGET_PALCE_TOP = 'home.top';

    /**
     * Widget place bottom
     *
     * @var string
     */
    const WIDGET_PALCE_BOTTOM = 'home.bottom';

    /**
     * Autocomplete list limit
     *
     * @var integer
     */
    const AUTOCOMPLETE_LIMIT = 10;

    /**
     * Limits on the number of items per page for home page
     *
     * @var array
     */
    public static $home_show_limit = [8, 16, 32, self::SHOW_LIMIT_ALL];

    /**
     * Limits on the number of items per page for search page
     *
     * @var array
     */
    public static $search_show_limit = [6, 12, 24, self::SHOW_LIMIT_ALL];

    /**
     * Sort items by field
     *
     * @var array
     */
    public static $sort_by_field = [
        'name'        => [
            'title' => 'Item name',
            'name'  => 'Name'
        ],
        'date_update' => [
            'title' => 'Last updated item',
            'name'  => 'Update'
        ],
        'date_start'  => [
            'title' => 'Start date of production',
            'name'  => 'Date start'
        ],
        'date_end'    => [
            'title' => 'End date of production',
            'name'  => 'Date end'
        ]
    ];

    /**
     * Sort direction
     *
     * @var array
     */
    public static $sort_direction = [
        'DESC' => 'Descending',
        'ASC'  => 'Ascending'
    ];

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

        // get items limit
        $limit = (int)$request->get('limit', self::HOME_ITEMS_PER_PAGE);
        $limit = in_array($limit, self::$home_show_limit) ? $limit : self::HOME_ITEMS_PER_PAGE;

        /* @var $repository \AnimeDb\Bundle\CatalogBundle\Repository\Item */
        $repository = $this->getDoctrine()->getRepository('AnimeDbCatalogBundle:Item');

        $pagination = null;
        // show not all items
        if ($limit != self::SHOW_LIMIT_ALL) {

            $that = $this;
            $pagination = $this->get('anime_db.pagination')->createNavigation(
                ceil($repository->count()/$limit),
                $current_page,
                Pagination::DEFAULT_LIST_LENGTH,
                function ($page) use ($that) {
                    return $that->generateUrl('home', ['page' => $page]);
                },
                $this->generateUrl('home')
            );
        }

        // get items
        $items = $repository->getList(
            ($limit != self::SHOW_LIMIT_ALL ? $limit : 0),
            ($limit != self::SHOW_LIMIT_ALL ? ($current_page - 1) * $limit : 0)
        );

        // assembly parameters limit output
        $show_limit = [];
        foreach (self::$home_show_limit as $value) {
            $show_limit[] = [
                'link' => $this->generateUrl('home', ['limit' => $value]),
                'name' => $value != -1 ? $value : 'All',
                'count' => $value,
                'current' => $limit == $value
            ];
        }

        return $this->render('AnimeDbCatalogBundle:Home:index.html.twig', [
            'items' => $items,
            'show_limit' => $show_limit,
            'pagination' => $pagination,
            'widget_top' => self::WIDGET_PALCE_TOP,
            'widget_bottom' => self::WIDGET_PALCE_BOTTOM
        ]);
    }

    /**
     * Search simple form
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchSimpleFormAction()
    {
        $form = new SearchSimple($this->generateUrl('home_autocomplete_name'));
        return $this->render('AnimeDbCatalogBundle:Home:searchSimpleForm.html.twig', [
            'form' => $this->createForm($form)->createView(),
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
        $term = strtolower($request->get('term'));
        /* @var $service \AnimeDb\Bundle\CatalogBundle\Service\Search\Manager */
        $service = $this->get('anime_db.search');
        $result = $service->searchByName($term, self::AUTOCOMPLETE_LIMIT);

        $list = [];
        /* @var $item \AnimeDb\Bundle\CatalogBundle\Entity\Item */
        foreach ($result as $item) {
            if (strpos(strtolower($item->getName()), $term) === 0) {
                $list[] = $item->getName();
            } else {
                /* @var $name \AnimeDb\Bundle\CatalogBundle\Entity\Name */
                foreach ($item->getNames() as $name) {
                    if (strpos(strtolower($name->getName()), $term) === 0) {
                        $list[] = $name->getName();
                        break;
                    }
                }
            }
        }
        return new JsonResponse($list);
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
        $data = new SearchEntity();
        /* @var $form \Symfony\Component\Form\Form */
        $form = $this->createForm(new SearchForm($this->generateUrl('home_autocomplete_name')), $data);
        $items = [];
        $pagination = null;
        // list items controls
        $show_limit = null;
        $sort_by = null;
        $sort_direction = null;

        if ($request->query->count()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                /* @var $service \AnimeDb\Bundle\CatalogBundle\Service\Search\Manager */
                $service = $this->get('anime_db.search');

                // current page for paging
                $current_page = $request->get('page', 1);
                $current_page = $current_page > 1 ? $current_page : 1;

                // get items limit
                $limit = (int)$request->get('limit', self::SEARCH_ITEMS_PER_PAGE);
                $limit = in_array($limit, self::$search_show_limit) ? $limit : self::SEARCH_ITEMS_PER_PAGE;

                // get order
                $current_sort_by = ManagerSearch::getValidSortColumn($request->get('sort_by'));
                $current_sort_direction = ManagerSearch::getValidSortDirection($request->get('sort_direction'));

                // do search
                $result = $service->search(
                    $data,
                    ($limit != self::SHOW_LIMIT_ALL ? $limit : 0),
                    ($limit != self::SHOW_LIMIT_ALL ? ($current_page - 1) * $limit : 0),
                    $current_sort_by,
                    $current_sort_direction
                );
                $items = $result['list'];

                // build sort params for tamplate
                $sort_by = [];
                foreach (self::$sort_by_field as $field => $info) {
                    $sort_by[] = [
                        'name' => $info['name'],
                        'title' => $info['title'],
                        'current' => $current_sort_by == $field,
                        'link' => $this->generateUrl(
                            'home_search',
                            array_merge($request->query->all(), ['sort_by' => $field])
                        )
                    ];
                }
                $sort_direction['type'] = ($current_sort_direction == 'ASC' ? 'DESC' : 'ASC');
                $sort_direction['link'] = $this->generateUrl(
                    'home_search',
                    array_merge($request->query->all(), ['sort_direction' => $sort_direction['type']])
                );

                if ($limit != self::SHOW_LIMIT_ALL) {
                    // build pagination
                    $query = $request->query->all();
                    if (isset($query['page'])) {
                        unset($query['page']);
                    }
                    $that = $this;
                    $pagination = $this->get('anime_db.pagination')->createNavigation(
                        ceil($result['total']/$limit),
                        $current_page,
                        Pagination::DEFAULT_LIST_LENGTH,
                        function ($page) use ($that, $query) {
                            return $that->generateUrl(
                                'home_search',
                                array_merge($query, ['page' => $page])
                            );
                        },
                        $this->generateUrl('home_search', $query)
                    );
                }

                // assembly parameters limit output
                foreach (self::$search_show_limit as $value) {
                    $show_limit[] = [
                        'link' => $this->generateUrl(
                            'home_search',
                            array_merge($request->query->all(), ['limit' => $value])
                        ),
                        'name' => $value != -1 ? $value : 'All',
                        'count' => $value,
                        'current' => !empty($limit) && $limit == $value
                    ];
                }
            }
        }

        return $this->render('AnimeDbCatalogBundle:Home:search.html.twig', [
            'form'  => $form->createView(),
            'items' => $items,
            'show_limit' => $show_limit,
            'pagination' => $pagination,
            'sort_by' => $sort_by,
            'sort_direction' => $sort_direction
        ]);
    }

    /**
     * General settings
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function settingsAction(Request $request)
    {
        $entity = new GeneralEntity();
        $entity->setSerialNumber($this->container->getParameter('serial_number'));
        $entity->setTaskScheduler($this->container->getParameter('task_scheduler')['enabled']);
        $entity->setDefaultSearch($this->container->getParameter('default_search'));
        $entity->setLocale($request->getLocale());

        /* @var $form \Symfony\Component\Form\Form */
        $form = $this->createForm(new GeneralForm($this->get('anime_db.plugin.search_fill')), $entity);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                // update params
                $file = $this->container->getParameter('kernel.root_dir').'/config/parameters.yml';
                $parameters = Yaml::parse($file);
                $parameters['parameters']['serial_number'] = $entity->getSerialNumber();
                $parameters['parameters']['task_scheduler']['enabled'] = $entity->getTaskScheduler();
                $parameters['parameters']['default_search'] = $entity->getDefaultSearch();
                file_put_contents($file, Yaml::dump($parameters));
                // change locale
                $this->get('anime_db.listener.request')->setLocale($request, $entity->getLocale());

                return $this->redirect($this->generateUrl('home_settings'));
            }
        }

        return $this->render('AnimeDbCatalogBundle:Home:settings.html.twig', [
            'form'  => $form->createView()
        ]);
    }
}