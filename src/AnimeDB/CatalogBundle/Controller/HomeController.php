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

/**
 * The controller the main page of the catalog
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
        // TODO требуется реализация
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
            ->add('q', 'text', array('label' => $this->get('translator')->trans('Search')))
            ->getForm();

        return $this->render('AnimeDBCatalogBundle:Home:searchSimpleForm.html.twig', array(
                'form' => $form->createView(),
        ));
    }

    /**
     * Select by alphabet
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function alphabetAction() {
        // TODO требуется реализация
        return $this->render('AnimeDBCatalogBundle:Home:alphabet.html.twig');
    }

    /**
     * Select by category
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function selectionAction() {
        // TODO требуется реализация
        return $this->render('AnimeDBCatalogBundle:Home:selection.html.twig');
    }
}