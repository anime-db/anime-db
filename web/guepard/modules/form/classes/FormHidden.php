<?php

/**
 * Класс описывает элемент ввода формы
 * 
 * @license GNU GPL Version 3
 * @copyright 2009, Peter Gribanov
 * @link http://peter-gribanov.ru/license
 * @package	FormManager
 * @author	Peter Gribanov
 * @since	09.09.2011
 * @version	1.1
 */
class FormHidden extends FormElement {

	/**
	 * Выводит поле
	 * 
	 * @return void
	 */
	public function draw(){
		include Form::getTemplatePath('hidden.element.php');
	}

}