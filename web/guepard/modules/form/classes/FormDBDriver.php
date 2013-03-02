<?php

/**
 * Интерфейс для драйвера работы с БД
 * 
 * @license GNU GPL Version 3
 * @copyright 2009, Peter Gribanov
 * @link http://peter-gribanov.ru/license
 * @package	FormManager
 * @author	Peter Gribanov
 * @since	08.09.2011
 * @version	1.0
 */
interface FormDBDriver {

	/**
	 * Подготавливает запрос к исполненияю и выполняет его
	 * 
	 * @param string $statement
	 * @param array $input_parameters
	 * @return FormDBDriver
	 */
	public function prepare($statement, $input_parameters=null);

	/**
	 * Возвращает одну запись из результата запроса
	 * 
	 * @return mixed
	 */
	public function fetch();

}