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

/**
 * Main page of the catalog
 *
 * @package AnimeDB\CatalogBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class HomeController extends Controller
{
    /**
     * Home
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        // TODO requires the implementation of
        return $this->render('AnimeDBCatalogBundle:Home:index.html.twig', array('name' => 'Test'));
    }

    /**
     * Search simple form
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchSimpleFormAction()
    {
        /* @var $form \Symfony\Component\Form\Form */
        $form = $this->createFormBuilder()
            ->add('q', 'genemu_jqueryautocomplete_text', array(
                'label' => 'Search',
                'route_name' => 'home_autocomplete_name',
            ))
            ->getForm();

        return $this->render('AnimeDBCatalogBundle:Home:searchSimpleForm.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Autocomplete name
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function autocompleteNameAction() {
        $term = $this->getRequest()->get('term');

        // TODO do search
        $value = array('Foo', 'Bar');

        return new JsonResponse(json_encode($value));
    }

    /**
     * Select by category
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function selectionAction() {
        // TODO requires the implementation of
        return $this->render('AnimeDBCatalogBundle:Home:selection.html.twig');
    }
}