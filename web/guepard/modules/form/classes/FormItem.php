<?php

/**
 * Интерфейс элиментов формы
 * 
 * @license GNU GPL Version 3
 * @copyright 2009, Peter Gribanov
 * @link http://peter-gribanov.ru/license
 * @package	FormManager
 * @author	Peter Gribanov
 * @since	25.11.2010
 * @version	1.0
 */
interface FormItem {

	/**
	 * Устанавливает форму к которой пренадлежыт коллекция
	 * Метод предназначен для внутреннего использования
	 * 
	 * @param Form $form
	 * @return FormItem
	 */
	public function setForm(Form $form);
	
	/**
	 * Производит проверку переданных данных 
	 *
	 * @return void
	 */
	public function valid();

	/**
	 * Рисует коллекцию элиментов
	 * 
	 * @return void
	 */
	public function draw();

	/**
	 * Возвращает сообщение из языковой темы
	 * 
	 * @param string $post
	 * @return string
	 */
	public function getLangPost($post);

}