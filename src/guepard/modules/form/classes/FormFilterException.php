<?php

/**
 * Класс исключений для фильтров
 * 
 * @license GNU GPL Version 3
 * @copyright 2009, Peter Gribanov
 * @link http://peter-gribanov.ru/license
 * @package	FormManager
 * @author	Peter Gribanov
 * @since	25.11.2010
 * @version	1.1
 */
class FormFilterException extends Exception {

	/**
	 * Объект поля формы
	 * 
	 * @var FormElement
	 */
	private $element;

	/**
	 * Параметры фильтра
	 * 
	 * @var array
	 */
	private $filter;


	/**
	 * Конструктор
	 * 
	 * @param string $message
	 * @param FormElement $element
	 * @param array $filter
	 * @return void
	 */
	public function __construct($message, FormElement & $element, & $filter){
		parent::__construct($message, 0);
		$this->element = & $element;
		$this->filter = & $filter;
	}

	/**
	 * Возвращает объект поля формы
	 * 
	 * @return FormElement
	 */
	public function getElement(){
		return $this->element;
	}

	/**
	 * Возвращает имя фильтра
	 * 
	 * @return string
	 */
	public function getFilterName(){
		return $this->filter[0];
	}

	/**
	 * Возвращает параметры фильтра
	 * 
	 * @return array
	 */
	public function getFilterParams(){
		return $this->filter[1];
	}

}