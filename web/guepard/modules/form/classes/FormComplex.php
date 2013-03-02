<?php

/**
 * Интерфейс для комплексных компонентов использующих компонент формы
 * 
 * @license GNU GPL Version 3
 * @copyright 2009, Peter Gribanov
 * @link http://peter-gribanov.ru/license
 * @package	FormManager
 * @author	Peter Gribanov
 * @since	07.09.2011
 * @version	1.0
 */
interface FormComplex {

	/**
	 * Устанавливает объект формы
	 * 
	 * @param Form $form
	 * @return void
	 */
	public function setForm(Form $form);

}