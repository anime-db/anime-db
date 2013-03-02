<?php

/**
 * Клас является адаптором для работы с базой данных
 * 
 * @license GNU GPL Version 3
 * @copyright 2009, Peter Gribanov
 * @link http://peter-gribanov.ru/license
 * @package	FormManager
 * @author	Peter Gribanov
 * @since	08.09.2011
 * @version	1.1
 */
class FormDB {

	/**
	 * Поток запроса
	 * 
	 * @var FormDBDriver
	 */
	private static $driver;


	/**
	 * Устанавливает драйвер для работы с БД
	 * 
	 * @param FormDBDriver $driver
	 * @return void
	 */
	public static function setDBDriver(FormDBDriver $driver){
		self::$driver = $driver;
	}

	/**
	 * Подготавливает запрос к исполненияю и выполняет его
	 * 
	 * @param string $statement
	 * @param array $input_parameters
	 * @return FormDBDriver
	 */
	public static function prepare($statement, $input_parameters=null){
		return self::$driver->prepare($statement, $input_parameters);
	}

}