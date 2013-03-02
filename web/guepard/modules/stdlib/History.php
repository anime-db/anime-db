<?php
namespace stdlib;

/**
 * Класс для управления историей посощений
 * 
 * @package	Standard Library
 * @author	Peter Gribanov
 * @since	04.12.2010
 * @version	3.1
 */
class History {

	/**
	 * Конструктор
	 * 
	 * @return	void
	 */
	private function __construct(){
	}

	/**
	 * Возвращает объект коллекции ссылок для указанного индекса
	 * 
	 * @param	string
	 * @return	\stdlib\HistoryCollection
	 */
	public static function instance($index='main'){
		if (!isset($_SESSION['HISTORY_POSITION'])){
			$_SESSION['HISTORY_POSITION'] = array();
		}
		if (!isset($_SESSION['HISTORY_POSITION'][$index])){
			$_SESSION['HISTORY_POSITION'][$index] = new HistoryCollection();
		}
		return $_SESSION['HISTORY_POSITION'][$index];
	}

	/**
	 * Возвращает последнюю ссылку для указаного индекса
	 * 
	 * @param	string
	 * @return	string
	 */
	public static function get($index='main'){
		return self::instance($index)->getIterator()->current();
	}

	/**
	 * Устанавливает новую ссылку для указанного индекса
	 * 
	 * @param	string
	 * @return	boolen
	 */
	public static function set($index='main'){
		return self::instance($index)->set();
	}

	/**
	 * Удаляет коллекцию ссылок с указанным индексом
	 * 
	 * @param	string
	 * @return	void
	 */
	public static function remove($index='main'){
		if (isset($_SESSION['HISTORY_POSITION'][$index])){
			unset($_SESSION['HISTORY_POSITION'][$index]);
		}
	}

	/**
	 * HTTP редирект на предыдущую страницу или страницу указанную по умолчанию
	 * 
	 * @param	string
	 * @return	void
	 */
	public static function goBack($default='/'){
		// тикущая страница
		$curr = $_SERVER['SERVER_PROTOCOL'][4]=='S' ? 'https' : 'http';
		$curr = $curr.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

		if (getenv('HTTP_REFERER') && (getenv('HTTP_REFERER') != $curr)){
			$default = getenv('HTTP_REFERER');
		}
		redirect($default);
	}

}