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
 * Контроллер управления записью
 *
 * @package AnimeDB\CatalogBundle\Controller
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ItemController extends Controller
{

	/**
	 * Просмотр записи
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
	 * Добавление записи
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function addAction()
	{
		// TODO требуется реализация
		return $this->render('AnimeDBCatalogBundle:Item:add.html.twig');
	}

	/**
	 * Изменение записи
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
	 * Удаление записи
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