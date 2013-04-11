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
 * Контроллер главной страници каталога
 *
 * @package AnimeDB\CatalogBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class HomeController extends Controller
{

	/**
	 * Главная
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function indexAction()
	{
		// TODO требуется реализация
		return $this->render('AnimeDBCatalogBundle:Home:index.html.twig', array('name' => 'Test'));
	}

	/**
	 * Поиск записи
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function searchSimpleFormAction()
	{
		$form = $this->createFormBuilder()
		->add('q', 'text', array('label' => $this->get('translator')->trans('Search')))
		->getForm();

		return $this->render('AnimeDBCatalogBundle:Home:searchSimpleForm.html.twig', array(
			'form' => $form->createView(),
		));
	}

	/**
	 * Выбор по алфавиту
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function alphabetAction() {
		// TODO требуется реализация
		return $this->render('AnimeDBCatalogBundle:Home:alphabet.html.twig');
	}

	/**
	 * Выбор по категории
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function selectionAction() {
		// TODO требуется реализация
		return $this->render('AnimeDBCatalogBundle:Home:selection.html.twig');
	}

}