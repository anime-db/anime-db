<?php

/**
 * Прямой итератор
 * 
 * @license GNU GPL Version 3
 * @copyright 2009, Peter Gribanov
 * @link http://peter-gribanov.ru/license
 * @package	FormManager
 * @author	Peter Gribanov
 * @since	24.11.2010
 * @version	1.0
 */
class FormDirectIterator implements Iterator {

	/**
	 * Список данных
	 * 
	 * @var array
	 */
	private $var = array();


	/**
	 * Конструктор
	 * 
	 * @param array $array
	 * @return void
	 */
	public function __construct(& $array){
		if (is_array($array)) $this->var = & $array;
	}

	/**
	 * Устанавливает внутренний указатель на первый элимент
	 * 
	 * @return void
	 */
	public function rewind(){
		reset($this->var);
	}

	/**
	 * Возвращает текущий элимент
	 * 
	 * @return mixed
	 */
	public function current(){
		return current($this->var);
	}

	/**
	 * Возвращает индекс текущей позиции
	 * 
	 * @return mixed
	 */
	public function key(){
		return key($this->var);
	}

	/**
	 * Передвигает вперед внутренний указатель массива
	 * 
	 * @return mixed
	 */
	public function next(){
		return next($this->var);
	}

	/**
	 * Проверяет, существует ли элемент на текущей позиции
	 * 
	 * @return boolean
	 */
	public function valid(){
		return $this->current()!==false;
	}

}