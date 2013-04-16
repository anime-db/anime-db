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
 * Item
 *
 * @package AnimeDB\CatalogBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ItemController extends Controller
{
    /**
     * Show item
     *
     * @param integer $id ID записи
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($id)
    {
        // TODO требуется реализация
        return $this->render('AnimeDBCatalogBundle:Item:show.html.twig', array('name' => 'Test'));
    }

    /**
     * Add item
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction()
    {
        /* @var $chain \AnimeDB\CatalogBundle\Service\Autofill\Chain */
        $chain = $this->get('anime_db_catalog.autofill.chain');

        /* @var $vender \Symfony\Component\Form\Form */
        $search = $this->createFormBuilder()
            ->add('name', 'text', array(
                'label' => $this->get('translator')->trans('Name'),
                'attr'=> array(
                    'placeholder' => 'One Piece',
                ),
            ))
            ->add('filler', 'choice', array(
                'label' => $this->get('translator')->trans('Source'),
                'choices' => $chain->getFillerTetles()
            ))
            ->getForm();

        /* @var $source \Symfony\Component\Form\Form */
        $source = $this->createFormBuilder()
            ->add('url', 'text', array(
                'label' => $this->get('translator')->trans('URL address'),
                'attr'=> array(
                    'placeholder' => 'http://',
                ),
            ))
            ->getForm();

        return $this->render('AnimeDBCatalogBundle:Item:add.html.twig', array(
            'source_form' => $source->createView(),
            'search_form' => $search->createView(),
        ));
    }

    /**
     * Addition form
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function additionFormAction()
    {
        // TODO требуется реализация
        return $this->render('AnimeDBCatalogBundle:Item:addition-form.html.twig');
    }

    /**
     * Change item
     *
     * @param integer $id ID записи
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function changeAction($id)
    {
        // TODO требуется реализация
        return $this->render('AnimeDBCatalogBundle:Item:change.html.twig');
    }

    /**
     * Remove item
     *
     * @param integer $id ID записи
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeAction($id)
    {
        // TODO требуется реализация
        return $this->redirect($this->generateUrl('home'));
    }
}