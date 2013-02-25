<?php
namespace stdlib;

/**
 * Класс для роботы с куками
 * 
 * @package	Standard Library
 * @author	Peter Gribanov
 * @since	10.05.2011
 * @version	2.1
 */
class Cookie {

	/**
	 * Конструктор
	 * 
	 * @return	void
	 */
	private function __construct(){
	}

	/**
	 * Возвращаент записи из куков по ее идентификатоу
	 * 
	 * @since   2.1
	 * @param   string	Имя
	 * @return  mixed
	 */
	public static function & get($name){
		return $_COOKIE[$name];
	}

	/**
	 * Установка записи в куки
	 *
	 * Абстрагирование позволяет сделать некоторые проверки и т.д.
	 *
	 * @since   2.1
	 * @param   string	Имя
	 * @param   string	Значение
	 * @param   integer	Устанавить на год?
	 * @param   integer	Число дней
	 * @return	boolen
	 */
	public static function set($name, $value='', $sticky=1, $expires_days=0){
		// получение времени жизни кук
		if ($sticky == 1){
			$expires = time() + (60*60*24*365);
		} elseif ($expires_days){
			$expires = time() + (60*60*24*$expires_days);
		} else {
			return false;
		}
		// установка кук
		@setcookie($name, $value, $expires);
		return true;
	}

	/**
	 * Удаляет запись из кук с данным идентификатором
	 * 
	 * @param	string
	 * @param   integer	Устанавить на год?
	 * @param   integer	Число дней
	 * @return	boolen
	 */
	public static function remove($name, $sticky=1, $expires_days=0){
		if (isset($_COOKIE[$name])){
			if ($sticky == 1){
				$expires_days = 365;
			}
			self::set($name, '', 0, -$expires_days);
		}
		return false;
	}

	/**
	 * Проверяет есть ли в куках запись с данным идентификатором
	 *
	 * @param	string
	 * @return	boolen
	 */
	public static function isRegistered($name){
		return isset($_COOKIE[$name]);
	}

}