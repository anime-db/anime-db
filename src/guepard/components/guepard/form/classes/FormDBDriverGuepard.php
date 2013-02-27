<?php
require_once ROOT.G_ROOT.'modules/form/classes/FormDBDriver.php';

/**
 * Интерфейс для драйвера работы с БД
 * 
 * @package	Form
 * @author	Peter Gribanov
 * @since	08.09.2011
 * @version	1.0
 */
class FormDBDriverGuepard implements FormDBDriver {

	/**
	 * Поток запроса
	 * 
	 * @var DBQuery
	 */
	private $stream;

	/**
	 * Подготавливает запрос к исполненияю и выполняет его
	 * 
	 * @param string $statement
	 * @param array $input_parameters
	 * @return FormDBDriverGuepard
	 */
	public function prepare($statement, $input_parameters=null){
		$this->stream = DB::prepare($statement);
		$this->stream->execute($input_parameters);
		return $this;
	}

	/**
	 * Возвращает одну запись из результата запроса
	 * 
	 * @return mixed
	 */
	public function fetch(){
		return $this->stream->fetch();
	}

}