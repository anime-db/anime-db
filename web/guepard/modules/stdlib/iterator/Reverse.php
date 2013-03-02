<?php
namespace stdlib\iterator;

/**
 * Обратный итератор
 * 
 * @package	Standard Library
 * @author	Peter Gribanov
 * @since	02.12.10
 * @version	1.1
 */
class Reverse implements \Iterator {

	/**
	 * Список записей
	 * 
	 * @var	array
	 */
	private $list = array();


	/**
	 * Конструктор
	 * 
	 * @param	array
	 * @return	void
	 */
	public function __construct(& $list){
		if (!is_array($array)){
			throw new \InvalidArgumentException('Reverse iterator only works with arrays.');
		}
		$this->list = & $array;
	}

	/**
	 * Устанавливает внутренний указатель на последний элимент
	 * 
	 * @return	void
	 */
	public function rewind(){
		end($this->list);
	}

	/**
	 * Возвращает текущий элимент
	 * 
	 * @return	mixed
	 */
	public function current(){
		return current($this->list);
	}

	/**
	 * Возвращает индекс текущей позиции
	 * 
	 * @return	mixed
	 */
	public function key(){
		return key($this->list);
	}

	/**
	 * Передвигает назад внутренний указатель массива
	 * 
	 * @return	mixed
	 */
	public function next(){
		return prev($this->list);
	}

	/**
	 * Проверяет, существует ли элемент на текущей позиции
	 * 
	 * @return	boolean
	 */
	public function valid(){
		return $this->current()!==false;
	}

}