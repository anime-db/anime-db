<?php
namespace stdlib;

/**
 * Класс реестра позволяет в статическом режиме записывать данные различного типа в реестр
 * Также позволяет статично получить данные из реестра 
 *
 * @package	Standard Library
 * @author	Peter Gribanov
 * @since	09.10.2010
 * @version	1.6
 */
class Registry {

	/**
	 * Массив загруженных объектов
	 *
	 * @var	array
	 */
	protected static $objects = array();


	/**
	 * @param	string
	 * @return	array
	 */
	public function __construct($objects){
		return is_array($objects) ? $objects : array();
	}

	/**
	 * Установка нового объекта
	 *
	 * @param	string
	 * @param	mixed
	 * @return	void
	 */
	public static function set($index, $arg){
		self::$objects[$index] = $arg;
	}

	/**
	 * Получение зарегистрированного объекта по его идентификатоу
	 *
	 * @param	string
	 * @return	mixed
	 */
	public static function & get($index){
		return self::$objects[$index];
	}

	/**
	 * Удаляет зарегистрированный объект с данным идентификатором
	 * 
	 * @param	string
	 * @return	boolen
	 */
	public static function remove($index){
		if (self::isRegistered($index)){
			unset(self::$objects[$index]);
			return true;
		}
		return false;
	}

	/**
	 * Получение списка зарегистрированных объектов
	 *
	 * @return	array
	 */
	public static function getInstance(){
		return self::$objects;
	}

	/**
	 * Добавление списка объектов в список зарегистрированных объектов
	 *
	 * @param	array
	 * @return	void
	 */
	public static function setInstance($objects){
		self::$objects = array_merge(self::$objects,
			is_array($objects) ? $objects : array($objects));
	}

	/**
	 * Проверка зарегистрирован ли объект с данным идентификатором
	 *
	 * @param	string
	 * @return	boolen
	 */
	public static function isRegistered($index){
		return isset(self::$objects[$index]);
	}

}
?>