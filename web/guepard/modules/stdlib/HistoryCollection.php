<?php
namespace stdlib;

use stdlib\iterator\Direct;

/**
 * Класс для описания списка ссылок последних посещений
 * 
 * @package	Standard Library
 * @author	Peter Gribanov
 * @since	04.12.2010
 * @version	1.3
 */
class HistoryCollection implements IteratorAggregate, Serializable {

	/**
	 * Список ссылок
	 * Состоит из 10 записей
	 * 
	 * @var	array
	 */
	private $links = array();

	/**
	 * Итератор по списку ссылок
	 * 
	 * @var	\stdlib\iterator\Direct
	 */
	private $iterator;


	/**
	 * Конструктор
	 * 
	 * @return	void
	 */
	public function __construct(){
		$this->links = array_fill(0, 10, '');
	}

	/**
	 * Устанавливает новую ссылку
	 * 
	 * @return	\stdlib\HistoryCollection
	 */
	public function set(){
		// тикущая страница
		$curr = $_SERVER['SERVER_PROTOCOL'][4]=='S' ? 'https' : 'http';
		$curr = $curr.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

		if (getenv('HTTP_REFERER') && ($_SERVER['HTTP_REFERER'] != $curr)){
			array_unshift($this->links, $_SERVER['HTTP_REFERER']);
			unset($this->links[11]);
		}
		return $this;
	}

	/**
	 * Возвращает итератор по списку ссылок
	 * 
	 * @return	\stdlib\iterator\Direct
	 */
	public function getIterator(){
		if (!$this->iterator){
			$this->iterator = new Direct($this->links);
		}
		return $this->iterator;
	}

	/**
	 * Сериализует коллекцию
	 * 
	 * @return	string
	 */
	public function serialize(){
		return serialize($this->links);
	}

	/**
	 * Десириализует коллекцию
	 * 
	 * @param	string
	 * @return	void
	 */
	public function unserialize($serialized){
		$this->links = unserialize($serialized);
	}

}