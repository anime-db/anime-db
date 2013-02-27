<?php
// элимент результата запроса
require 'DBQueryItem.php';


/**
 * Декоратор для PDOStatement
 * 
 * @license GNU GPL Version 3
 * @copyright 2011, Peter Gribanov
 * @link http://peter-gribanov.ru/license
 * @package	DB
 * @author	Peter Gribanov
 * @since	14.09.2011
 * @version	1.6
 */
class DBQuery extends PDOStatement {

	/**
	 * Поток запроса
	 * 
	 * @var PDOStatement
	 */
	private $stream;


	/**
	 * Конструктор
	 * 
	 * @param PDOStatement
	 * @return void
	 */
	public function __construct(PDOStatement $stream){
		$this->stream = $stream;
		$this->setFetchMode(PDO::FETCH_CLASS, 'DBQueryItem');
	}

	/**
	 * Bind a column to a PHP variable 
	 * 
	 * @param mixed $column
	 * @param mixed $param
	 * @param mixed $type
	 * @param integer $maxlen
	 * @param mixed $driverdata
	 * @return boolen
	 */
	public function bindColumn($column, & $param, $type=null, $maxlen=null, $driverdata=null){
		return $this->stream->bindColumn($column, $param, $type, $maxlen, $driverdata);
	}

	/**
	 * Добавляет параметр запроса
	 * 
	 * @param mixed $parameter
	 * @param mixed $variable
	 * @param integer $data_type
	 * @param integer $length
	 * @param mixed $driver_options
	 * @return boolen
	 */
	public function bindParam($parameter, & $variable, $data_type=null, $length=null, $driver_options=null){
		$this->stream->bindParam($parameter, $variable, $data_type, $length, $driver_options);
	}

	/**
	 * Добавляет параметр запроса
	 * 
	 * @param mixed $parameter
	 * @param mixed $value
	 * @param integer $data_type
	 * @return boolen
	 */
	public function bindValue($parameter, $value, $data_type=null){
		return $this->stream->bindValue($parameter, $value, $data_type);
	}

	/**
	 * Closes the cursor, enabling the statement to be executed again
	 * 
	 * @return boolen
	 */
	public function closeCursor(){
		return $this->stream->closeCursor();
	}

	/**
	 * Возвращает число столбцов полученных в запросе
	 * 
	 * @return integer
	 */
	public function columnCount(){
		return $this->stream->columnCount();
	}

	/**
	 * Dump an SQL prepared command
	 * 
	 * @return boolen
	 */
	public function debugDumpParams(){
		return $this->stream->debugDumpParams();
	}

	/**
	 * Возвращает код ошибки
	 * 
	 * @return string
	 */
	public function errorCode(){
		return $this->stream->errorCode();
	}

	/**
	 * Возвращает информацию об ошибке
	 * 
	 * @return array
	 */
	public function errorInfo(){
		return $this->stream->errorInfo();
	}

	/**
	 * Выполняет запрос
	 * 
	 * @param array $input_parameters
	 * @return boolen
	 */
	public function execute($input_parameters=null){
		if (($ex=$this->stream->execute($input_parameters))===false){
			$query = & $this;
			require ROOT.G_ROOT.'modules/main/includes/dbquery_error.php';
		}
		return $ex;
	}

	/**
	 * Возвращает один элемент
	 * 
	 * @param integer $fetch_style
	 * @param integer $cursor_orientation
	 * @param integer $cursor_offset
	 * @return mixed
	 */
	public function fetch($fetch_style=null, $cursor_orientation=null, $cursor_offset=null){
		return $this->stream->fetch(($fetch_style==PDO::FETCH_OBJ ? PDO::FETCH_CLASS : $fetch_style), $cursor_orientation, $cursor_offset);
	}

	/**
	 * Returns an array containing all of the result set rows 
	 * 
	 * @param integer $fetch_style
	 * @param integer $column_index
	 * @param integer $ctor_args
	 * @return mixed
	 */
	public function fetchAll($fetch_style=null, $column_index=null, $ctor_args=null){
//		return $this->stream->fetchAll($fetch_style, $column_index, $ctor_args);
		/*
		// категорически ненравится такое решение, но иначе не работает((
		$params = array();
		if ($fetch_style) $params[] = $fetch_style;
		if ($column_index) $params[] = $column_index;
		if ($ctor_args) $params[] = $ctor_args;
		return call_user_func_array(array($this->stream, 'fetchAll'), $params);
		*/
		// этот вариант несколько лучше
		if ($ctor_args){
			return $this->stream->fetchAll($fetch_style, $column_index, $ctor_args);	
		} elseif ($column_index){
			return $this->stream->fetchAll($fetch_style, $column_index);
		} elseif ($fetch_style){
			return $this->stream->fetchAll($fetch_style);
		} else {
			return $this->stream->fetchAll();
		}
	}

	/**
	 * Returns a single column from the next row of a result set
	 * 
	 * @param integer $column_number
	 * @return string
	 */
	public function fetchColumn($column_number=0){
		return $this->stream->fetchColumn($column_number);
	}

	/**
	 * Fetches the next row and returns it as an object
	 * 
	 * @param string $class_name
	 * @param array $ctor_args
	 * @return mixid
	 */
	public function fetchObject($class_name=null, $ctor_args=null){
		return $this->stream->fetchObject($class_name ? $class_name : 'DBQueryItem', $ctor_args);
	}

	/**
	 * Retrieve a statement attribute
	 * 
	 * @param integer $attribute
	 * @return mixid
	 */
	public function getAttribute($attribute){
		return $this->stream->getAttribute($attribute);
	}

	/**
	 * Returns metadata for a column in a result set 
	 * 
	 * @param integer $column
	 * @return array
	 */
	public function getColumnMeta($column){
		return $this->stream->getColumnMeta($column);
	}

	/**
	 * Advances to the next rowset in a multi-rowset statement handle
	 * 
	 * @return boolen
	 */
	public function nextRowset(){
		return $this->stream->nextRowset();
	}

	/**
	 * Возвращает число строк полученных в запросе
	 * 
	 * @return integer
	 */
	public function rowCount(){
		return $this->stream->rowCount();
	}

	/**
	 * Set a statement attribute
	 * 
	 * @param integer $attribute
	 * @param mixid $value
	 * @return boolen
	 */
	public function setAttribute($attribute, $value){
		return $this->stream->setAttribute($attribute,$value);
	}

	/**
	 * Set the default fetch mode for this statement 
	 * 
	 * @param integer $mode
	 * @param mixid $classname
	 * @param array $ctorargs
	 * @return boolen
	 */
	public function setFetchMode($mode, $classname=null, $ctorargs=null){
		return $this->stream->setFetchMode($mode, $classname);
	}

}