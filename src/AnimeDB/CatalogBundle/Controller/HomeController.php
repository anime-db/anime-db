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
use AnimeDB\CatalogBundle\Entity\Type as TypeEntity;
use AnimeDB\CatalogBundle\Entity\Country as CountryEntity;
use AnimeDB\CatalogBundle\Entity\Genre as GenreEntity;
use AnimeDB\CatalogBundle\Entity\Storage as StorageEntity;
use Doctrine\ORM\Query\Expr;

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
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        // current page for paging
        $page = $request->get('page', 1);
        $current_page = $page > 0 ? $page-1 : 0;

        // get items
        $repository = $this->getDoctrine()->getRepository('AnimeDBCatalogBundle:Item');
        $query = $repository->createQueryBuilder('i')
            ->orderBy('i.id', 'ASC')
            ->setFirstResult($current_page * self::ITEMS_PER_PAGE)
            ->setMaxResults(self::ITEMS_PER_PAGE)
            ->getQuery();
        $items = $query->getResult();

        return $this->render('AnimeDBCatalogBundle:Home:index.html.twig', ['items' => $items]);
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
        $items = [];

        if ($request->query->count()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                // current page for paging
                $page = $request->get('page', 1);
                $current_page = $page > 0 ? $page-1 : 0;
                $data = $form->getData();

                // build query
                $repository = $this->getDoctrine()->getRepository('AnimeDBCatalogBundle:Item');
                /* @var $builder \Doctrine\ORM\QueryBuilder */
                $builder = $repository->createQueryBuilder('i')
                    ->orderBy('i.'.($data['sort'] ?: 'id'), 'ASC')
                    ->setFirstResult($current_page * self::ITEMS_PER_PAGE)
                    ->setMaxResults(self::ITEMS_PER_PAGE);

                // main name
                if ($data['name']) {
                    $builder->andWhere('i.name = :name')
                        ->setParameter('name', $data['name']);
                }
                // date start
                if ($data['date_start'] instanceof \DateTime) {
                    $builder->andWhere('i.date_start >= :date_start')
                        ->setParameter('date_start', $data['date_start']->format('Y-m-d'));
                }
                // date end
                if ($data['date_end'] instanceof \DateTime) {
                    $builder->andWhere('i.date_end <= :date_end')
                        ->setParameter('date_end', $data['date_end']->format('Y-m-d'));
                }
                // manufacturer
                if ($data['manufacturer'] instanceof CountryEntity) {
                    $builder->andWhere('i.manufacturer = :manufacturer')
                        ->setParameter('manufacturer', $data['manufacturer']->getId());
                }
                // storage
                if ($data['storage'] instanceof StorageEntity) {
                    $builder->andWhere('i.storage = :storage')
                        ->setParameter('storage', $data['storage']->getId());
                }
                // type
                if ($data['type'] instanceof TypeEntity) {
                    $builder->andWhere('i.type = :type')
                        ->setParameter('type', $data['type']->getId());
                }
                // genres
                if ($data['genres']->count()) {
                    $keys = [];
                    foreach ($data['genres'] as $key => $genre) {
                        $keys[] = ':genre'.$key;
                        $builder->setParameter('genre'.$key, $genre->getId());
                    }
                    $builder->innerJoin('i.genres', 'g')
                        ->andWhere('g.id IN ('.implode(',', $keys).')');
                }
                // get items
                $items = $builder->getQuery()->getResult();
            }
        }

        return $this->render('AnimeDBCatalogBundle:Home:search.html.twig', [
            'form'  => $form->createView(),
            'items' => $items,
        ]);
    }
}