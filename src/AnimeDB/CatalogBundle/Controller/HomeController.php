<?php
/**
 * AnimeDB package
 *
 * @package   AnimeDB
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDB\CatalogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use AnimeDB\CatalogBundle\Form\SearchSimple;
use AnimeDB\CatalogBundle\Form\Search;
use Symfony\Component\HttpFoundation\Request;

/**
 * Main page of the catalog
 *
 * @package AnimeDB\CatalogBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class HomeController extends Controller
{
    /**
     * Items per page
     *
     * @var integer
     */
    const ITEMS_PER_PAGE = 6;

    /**
     * Home
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        // current page for paging
        $page = $this->getRequest()->get('page', 1);
        $current_page = $page > 0 ? $page-1 : 0;

        // get items
        $repository = $this->getDoctrine()->getRepository('AnimeDBCatalogBundle:Item');
        $query = $repository->createQueryBuilder('i')
            ->orderBy('i.id', 'ASC')
            ->setFirstResult($current_page*self::ITEMS_PER_PAGE)
            ->setMaxResults(self::ITEMS_PER_PAGE)
            ->getQuery();
        $items = $query->getResult();

        return $this->render('AnimeDBCatalogBundle:Home:index.html.twig', array('items' => $items));
    }

    /**
     * Search simple form
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchSimpleFormAction()
    {
        return $this->render('AnimeDBCatalogBundle:Home:searchSimpleForm.html.twig', array(
            'form' => $this->createForm(new SearchSimple())->createView(),
        ));
    }

    /**
     * Autocomplete name
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function autocompleteNameAction()
    {
        $term = $this->getRequest()->get('term');

        // TODO do search
        $value = array('Foo', 'Bar');

        return new JsonResponse(json_encode($value));
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

        if ($request->query->count()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                // TODO do search
                //$form->getData();
            }
        }

        return $this->render('AnimeDBCatalogBundle:Home:search.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}